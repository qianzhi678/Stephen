<?php
// admin/index.php (FINAL VERSION with Chart.js)

require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

// Security: Protect this entire page
check_login();
check_admin();


// --- 1. DATA PREPARATION (PHP Part) ---

// a) Fetch stats for the dashboard cards
$stmt_users = $pdo->query("SELECT COUNT(user_id) FROM users");
$total_users = $stmt_users->fetchColumn();

$stmt_subs = $pdo->query("SELECT COUNT(subscription_id) FROM subscriptions WHERE status = 'active'");
$active_subscriptions = $stmt_subs->fetchColumn();


// b) Prepare data specifically for the Chart.js chart
$chart_labels = []; // This will hold the dates (e.g., "Oct 20", "Oct 21")
$chart_data = [];   // This will hold the subscription counts for each date

// Loop through the last 7 days, including today
for ($i = 6; $i >= 0; $i--) {
    // Calculate the date for each day in the loop
    $date = date('Y-m-d', strtotime("-$i days"));
    
    // Add the formatted date (e.g., "Oct 20") to our labels array
    $chart_labels[] = date('M j', strtotime($date));
    
    // Prepare and execute a SQL query to count new subscriptions ONLY for that specific day
    $sql = "SELECT COUNT(subscription_id) FROM subscriptions WHERE DATE(start_date) = :date";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['date' => $date]);
    
    // Fetch the result (the count) and add it to our data array
    $count = $stmt->fetchColumn();
    $chart_data[] = $count;
}

// Now, convert the PHP arrays into a JSON string format.
// This is crucial because JavaScript can directly read and understand JSON.
$json_labels = json_encode($chart_labels);
$json_data = json_encode($chart_data);


// --- 2. HTML STRUCTURE (HTML Part) ---

// Include the standard site header
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h3>Admin Menu</h3>
        <nav>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="active">Dashboard</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/products.php">Manage Products</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/boxes.php">Manage Boxes</a></li>
                <li><a href="<?php echo BASE_URL; ?>/admin/manage-users.php">Manage Users</a></li>
            </ul>
        </nav>
    </aside>
    <main class="admin-content">
        <h2>Admin Dashboard</h2>
        <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h4>Total Users</h4>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-card">
                <h4>Active Subscriptions</h4>
                <p><?php echo $active_subscriptions; ?></p>
            </div>
        </div>
        
        <!-- This is the "canvas" where our chart will be drawn -->
        <article style="margin-top: 2rem;">
            <header>
                <h4>New Subscriptions (Last 7 Days)</h4>
            </header>
            <canvas id="salesChart"></canvas>
        </article>

    </main>
</div>


<!-- --- 3. CHART RENDERING (JavaScript Part) --- -->

<!-- First, we include the Chart.js library file that we downloaded earlier. -->
<!-- This makes the 'Chart' object available in our script. -->
<script src="<?php echo BASE_URL; ?>/assets/js/chart.umd.js"></script>

<script>
    // This script will run after the page has loaded.

    // a) Get a reference to our canvas element using its ID.
    const ctx = document.getElementById('salesChart');

    // b) Prepare the data object that Chart.js will use.
    // We are injecting the JSON strings we prepared in PHP directly into the JavaScript.
    const chartData = {
        // `labels` are the values for the X-axis (our dates)
        labels: <?php echo $json_labels; ?>,
        
        // `datasets` is an array of data series to plot. We only have one.
        datasets: [{
            label: 'New Subscriptions', // This appears in the tooltip
            data: <?php echo $json_data; ?>, // The values for the Y-axis (our counts)
            borderColor: 'rgb(75, 192, 192)', // Line color
            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Fill color under the line
            fill: true, // Show the fill color
            tension: 0.1 // Makes the line slightly curved
        }]
    };

    // c) Create the new chart instance.
    // We tell Chart.js: "Please draw a chart on our canvas (ctx),
    // make it a 'line' chart, and use the data we've prepared (chartData)."
    new Chart(ctx, {
        type: 'line', // You can change this to 'bar' for a bar chart
        data: chartData,
        options: {
            scales: {
                y: {
                    beginAtZero: true, // Make sure the Y-axis starts at 0
                    ticks: {
                        // This forces the Y-axis to only show whole numbers (e.g., 0, 1, 2)
                        // which is important because you can't have half a subscription.
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>


</body>
</html>