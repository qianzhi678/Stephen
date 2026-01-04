<?php
// checkout.php
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

check_login(); // Ensure user is logged in

// Check if a plan was selected from the previous page
if (!isset($_POST['plan_name']) || !isset($_POST['plan_price'])) {
    // If not, redirect back to the subscribe page
    header("Location: " . BASE_URL . "/subscribe.php");
    exit;
}

$plan_name = $_POST['plan_name'];
$plan_price = $_POST['plan_price'];
$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = "";

// Handle the final "payment" submission
if (isset($_POST['submit_payment'])) {
    // --- PAYMENT SIMULATION ---
    // In a real app, you would integrate a payment gateway like Stripe or PayPal here.
    // For this project, we will just assume the payment is successful.
    $payment_successful = true; 
    
    if ($payment_successful) {
        // Determine the next billing date based on the plan
        if ($plan_name == 'Annual') {
            $next_billing_date = date('Y-m-d', strtotime('+1 year'));
        } else { // Quarterly
            $next_billing_date = date('Y-m-d', strtotime('+3 months'));
        }

        // Insert the subscription record into the database
        $sql = "INSERT INTO subscriptions (user_id, plan_name, status, next_billing_date) 
                VALUES (:user_id, :plan_name, 'active', :next_billing_date)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id,
            'plan_name' => $plan_name,
            'next_billing_date' => $next_billing_date
        ]);

        $success_message = "Thank you! Your subscription is now active.";
    } else {
        $errors[] = "Your payment could not be processed. Please try again.";
    }
}

require_once ROOT_PATH . '/includes/header.php';
?>

<section style="max-width: 600px; margin: auto;">
    <header style="text-align: center;"><h2>Complete Your Subscription</h2></header>

    <?php if (!empty($success_message)): ?>
        <article style="text-align: center; border-color: #43a047;">
            <h3><?php echo $success_message; ?></h3>
            <p>Welcome to The Conscious Home Box family! Your first box is on its way (metaphorically).</p>
            <a href="<?php echo BASE_URL; ?>/" role="button">Back to Homepage</a>
        </article>
    <?php else: ?>
        <!-- Order Summary -->
        <article>
            <h4>Order Summary</h4>
            <p><strong>Plan:</strong> <?php echo htmlspecialchars($plan_name); ?></p>
            <p><strong>Total:</strong> $<?php echo htmlspecialchars($plan_price); ?></p>
        </article>

        <!-- Simulated Payment Form -->
        <article>
            <h4>Payment Details (Simulation)</h4>
            <p>No real payment will be processed. Click the button below to activate your subscription.</p>
            
            <form action="<?php echo BASE_URL; ?>/checkout.php" method="post">
                <!-- Pass the plan details through again -->
                <input type="hidden" name="plan_name" value="<?php echo htmlspecialchars($plan_name); ?>">
                <input type="hidden" name="plan_price" value="<?php echo htmlspecialchars($plan_price); ?>">

                <!-- Fake credit card fields for visual effect -->
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" placeholder="4242 4242 4242 4242" disabled>
                <div class="grid">
                    <label for="expiry">Expiry Date<input type="text" id="expiry" name="expiry" placeholder="MM/YY" disabled></label>
                    <label for="cvc">CVC<input type="text" id="cvc" name="cvc" placeholder="123" disabled></label>
                </div>
                
                <button type="submit" name="submit_payment">Activate My Subscription</button>
            </form>
            <?php if (!empty($errors)): ?>
                <p style="color: red;"><?php echo $errors[0]; ?></p>
            <?php endif; ?>
        </article>
    <?php endif; ?>

</section>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>