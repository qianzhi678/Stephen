<?php
// fluent-login.php (FINAL FIXED VERSION)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Load our PHP site's configuration and header
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/header.php';

// If user is already logged in, redirect them
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: " . BASE_URL . "/my-account.php");
    exit;
}

$email = $phone = "";
$error = "";

// --- WordPress Database Credentials ---
// Use variables instead of constants to avoid conflicts
$wp_db_host = '121.196.229.71'; // 
$wp_db_name = 'stephen';      // 
$wp_db_user = 'Stephen';      // 
$wp_db_password = 'Zhanlian123456!';//

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($email) || empty($phone)) {
        $error = "Please enter both your email and phone number.";
    } else {
        try {
            // 3. Establish a NEW connection to the WordPress database
            // Use the variables we just defined
            $wp_pdo = new PDO(
                "mysql:host=" . $wp_db_host . ";dbname=" . $wp_db_name, 
                $wp_db_user, 
                $wp_db_password
            );
            $wp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 4. Query the FluentCRM subscribers table
            $sql = "SELECT * FROM wp_fc_subscribers WHERE email = :email AND phone = :phone AND status = 'subscribed'";
            $stmt = $wp_pdo->prepare($sql);
            $stmt->execute(['email' => $email, 'phone' => $phone]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 5. Check if user was found
            if ($user) {
// SUCCESS! User exists in WordPress DB.

// --- NEW: Admin Role Check ---
// Define an array of administrator email addresses.
// You can add more emails here, separated by commas.
$admin_emails = [
    '2049204016@qq.com' // 
];

$user_role = 'customer'; // Default role is customer
if (in_array($user['email'], $admin_emails)) {
    $user_role = 'admin'; // If email matches, promote to admin!
}
// --- End of Admin Role Check ---


// Now, create a login session for our PHP site with the correct role.
if (session_status() == PHP_SESSION_NONE) { session_start(); }

$_SESSION["loggedin"] = true;
$_SESSION["user_id"] = $user['id'];
$_SESSION["username"] = $user['first_name'];
$_SESSION["email"] = $user['email'];
$_SESSION["role"] = $user_role; // Use the role we just determined

// Redirect to the main site's account page
header("Location: " . BASE_URL . "/my-account.php");
exit;
            } else {
                $error = "Invalid credentials or your subscription is not active.";
            }

        } catch (PDOException $e) {
            $error = "Error: Could not connect to the user database. Please check credentials and firewall settings.";
            // For debugging: die("Connection Failed: " . $e->getMessage());
        }
    }
}
?>

<!-- HTML Form for the new login page (No changes needed here) -->
<section style="max-width: 600px; margin: 4rem auto;">
    <article>
        <header style="text-align: center;">
            <h1>Login</h1>
            <p>Please use the email and phone number you registered with.</p>
        </header>
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="<?php echo BASE_URL; ?>/fluent-login.php" method="post">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>
            <button type="submit" class="contrast">Login</button>
        </form>
        <footer style="text-align: center; margin-top: 1rem;">
            <!-- IMPORTANT: Make sure this URL is correct -->
            <p>Don't have an account? <a href="http://121.196.229.71/register/">Register here</a>.</p>
        </footer>
    </article>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>