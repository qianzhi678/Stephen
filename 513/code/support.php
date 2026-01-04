<?php
// support.php - Customer Support Form
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

$success_msg = "";
$error_msg = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO support_tickets (user_name, user_email, subject, message) VALUES (:name, :email, :subject, :message)");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ]);
            $success_msg = "Thank you! Your ticket has been received. We will reply to $email shortly.";
        } catch (Exception $e) {
            $error_msg = "Something went wrong. Please try again.";
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}

require_once ROOT_PATH . '/includes/header.php';
?>

<section style="text-align: center; padding: 4rem 0; background-color: var(--bg-warm);">
    <h1 data-aos="fade-down">Customer Support</h1>
    <p data-aos="fade-up" data-aos-delay="100">Have a question about your order or our products? We're here to help.</p>
</section>

<section class="container" style="margin-top: 3rem; max-width: 800px;">
    
    <?php if ($success_msg): ?>
        <article style="background-color: #d4edda; color: #155724; border-color: #c3e6cb; text-align: center;">
            <h4>✅ Ticket Submitted</h4>
            <p><?php echo $success_msg; ?></p>
            <a href="<?php echo BASE_URL; ?>/" role="button" class="contrast">Back Home</a>
        </article>
    <?php else: ?>
    
        <article data-aos="fade-up">
            <?php if ($error_msg): ?>
                <p style="color: red; text-align: center;"><?php echo $error_msg; ?></p>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/support.php" method="post">
                <div class="grid">
                    <label>
                        Your Name *
                        <input type="text" name="name" required value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>">
                    </label>
                    <label>
                        Email Address *
                        <input type="email" name="email" required value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
                    </label>
                </div>

                <label>
                    Subject
                    <select name="subject">
                        <option value="Order Issue">Order Issue</option>
                        <option value="Product Question">Product Question</option>
                        <option value="Shipping Inquiry">Shipping Inquiry</option>
                        <option value="Other">Other</option>
                    </select>
                </label>

                <label>
                    Message *
                    <textarea name="message" rows="6" required placeholder="How can we help you today?"></textarea>
                </label>

                <button type="submit">Send Message</button>
            </form>
        </article>

        <div style="text-align: center; margin-top: 3rem; color: var(--text-light);">
            <p>Prefer to email us directly? <br> <strong>support@conscioushomebox.com</strong></p>
        </div>
    <?php endif; ?>

</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>