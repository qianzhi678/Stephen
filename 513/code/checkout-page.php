<?php
// checkout-page.php - Visual Checkout Confirmation
// Reads from JSON Session, does NOT write to DB yet.

require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

check_login();

// 1. Read JSON Cart
$cart_json = isset($_SESSION['cart_json']) ? $_SESSION['cart_json'] : '{}';
$cart_items = json_decode($cart_json, true);
$total = 0;

if (empty($cart_items)) {
    echo "<script>window.location.href = '" . BASE_URL . "/shop.php';</script>";
    exit;
}
?>

<section style="max-width: 800px; margin: 4rem auto;">
    <h2 style="text-align: center;">Checkout</h2>
    
    <!-- Order Summary -->
    <article>
        <header><strong>Order Summary</strong></header>
        <table class="striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): 
                    $subtotal = $item['price'] * $item['qty'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                    <td><strong style="color: var(--primary);">$<?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </article>

    <!-- Payment Simulation -->
    <article>
        <header><strong>Payment Details</strong></header>
        <p>This is a simulated checkout. No real money will be charged.</p>
        
        <!-- This form submits to the PROCESSING script -->
        <form action="<?php echo BASE_URL; ?>/process-order.php" method="post">
            <label>Cardholder Name</label>
            <input type="text" placeholder="John Doe" required>
            
            <label>Card Number</label>
            <input type="text" placeholder="0000 0000 0000 0000" disabled>
            
            <button type="submit" name="place_order" class="contrast">Place Order & Pay</button>
        </form>
    </article>
</section>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>