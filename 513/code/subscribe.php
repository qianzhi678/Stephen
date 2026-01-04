<?php
// subscribe.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

// --- SECURITY CHECK ---
// A user must be logged in to view subscription plans.
check_login();

// --- BUSINESS LOGIC ---
// Prevent already subscribed users from seeing this page again.
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE user_id = :user_id AND status = 'active'");
$stmt->execute(['user_id' => $user_id]);
$existing_subscription = $stmt->fetch();
?>

<section style="text-align: center;">
    <h1>Choose Your Plan</h1>
    <p>Join our community and start your sustainable journey today.</p>
</section>

<?php if ($existing_subscription): ?>
    <article style="text-align: center;">
        <header><h3>You already have an active subscription!</h3></header>
        <p>Thank you for being a valued member of our community.</p>
    </article>
<?php else: ?>
    <div class="grid">
        <article>
            <header>
                <h4>Quarterly Plan</h4>
                <h3>$49.99 / quarter</h3>
            </header>
            <p>A new box delivered to you every 3 months. Billed quarterly. Cancel anytime.</p>
            <footer>
                <form action="<?php echo BASE_URL; ?>/checkout.php" method="post">
                    <input type="hidden" name="plan_name" value="Quarterly">
                    <input type="hidden" name="plan_price" value="49.99">
                    <button type="submit">Choose Quarterly</button>
                </form>
            </footer>
        </article>
        <article>
            <header>
                <h4>Annual Plan (Best Value)</h4>
                <h3>$179.99 / year</h3>
            </header>
            <p>Pay once a year and save over 10%! Get 4 boxes, one each quarter.</p>
            <footer>
                <form action="<?php echo BASE_URL; ?>/checkout.php" method="post">
                    <input type="hidden" name="plan_name" value="Annual">
                    <input type="hidden" name="plan_price" value="179.99">
                    <button type="submit" class="contrast">Choose Annual & Save</button>
                </form>
            </footer>
        </article>
    </div>
<?php endif; ?>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>