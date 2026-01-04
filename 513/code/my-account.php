<?php
// my-account.php
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

// A user must be logged in to see their account page
check_login();

// Fetch the user's information from the database
$user_id = $_SESSION['user_id'];
$stmt_user = $pdo->prepare("SELECT username, email, created_at FROM users WHERE user_id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

// Fetch the user's subscription information
$stmt_sub = $pdo->prepare("SELECT plan_name, status, start_date, next_billing_date FROM subscriptions WHERE user_id = ? AND status = 'active'");
$stmt_sub->execute([$user_id]);
$subscription = $stmt_sub->fetch();

require_once ROOT_PATH . '/includes/header.php';
?>

<section>
    <header>
        <h2>My Account</h2>
        <p>Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</p>
    </header>

    <div class="grid">
        <!-- User Details Article -->
        <article>
            <header><h5>Account Details</h5></header>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </article>

        <!-- Subscription Details Article -->
        <article>
            <header><h5>Subscription Status</h5></header>
            <?php if ($subscription): ?>
                <p><strong>Current Plan:</strong> <?php echo htmlspecialchars($subscription['plan_name']); ?></p>
                <p><strong>Status:</strong> <span style="color: green; font-weight: bold;"><?php echo ucfirst(htmlspecialchars($subscription['status'])); ?></span></p>
                <p><strong>Next Billing Date:</strong> <?php echo date('F j, Y', strtotime($subscription['next_billing_date'])); ?></p>
            <?php else: ?>
                <p>You do not have an active subscription.</p>
                <a href="<?php echo BASE_URL; ?>/subscribe.php" role="button">View Plans</a>
            <?php endif; ?>
        </article>
    </div>
</section>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>