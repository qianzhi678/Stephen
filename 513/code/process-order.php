<?php
// process-order.php
// Logic: READS JSON from Session -> WRITES to MySQL Database -> CLEARS JSON

require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    
    // 1. Get Data from JSON Session
    $cart_json = isset($_SESSION['cart_json']) ? $_SESSION['cart_json'] : '{}';
    $cart_items = json_decode($cart_json, true);
    
    if (empty($cart_items)) {
        header("Location: " . BASE_URL . "/shop.php");
        exit;
    }

    // 2. Calculate Total
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += ($item['price'] * $item['qty']);
    }

    // 3. WRITE TO DATABASE (MySQL)
    try {
        $pdo->beginTransaction();

        // Insert into 'orders' table
        $sql = "INSERT INTO orders (user_id, total_amount, status, order_date) VALUES (:uid, :total, 'completed', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'total' => $total_amount
        ]);
        
        // (Optional: You could insert into an 'order_items' table here loop through $cart_items)

        $pdo->commit();

        // 4. CLEAR JSON DATA (The switch happens here!)
        unset($_SESSION['cart_json']); 
        // We delete the JSON data because it has been safely transferred to the DB.

        // 5. Show Success
        require_once ROOT_PATH . '/includes/header.php';
        ?>
        <section class="container" style="text-align: center; padding: 5rem 0;">
            <h1 style="color: var(--primary);">Order Successful!</h1>
            <p>Your order has been transferred from your temporary JSON cart to our permanent database.</p>
            <a href="<?php echo BASE_URL; ?>/shop.php" role="button">Continue Shopping</a>
        </section>
        <?php
        require_once ROOT_PATH . '/includes/footer.php';
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error processing order: " . $e->getMessage());
    }
} else {
    // If accessed directly without POST
    header("Location: " . BASE_URL . "/cart.php");
    exit;
}
?>