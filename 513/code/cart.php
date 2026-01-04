<?php
// cart.php - Shopping Cart (JSON Version)
// This page reads directly from the JSON session.

require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/header.php';

// 1. Retrieve and Decode JSON Cart
$cart_json = isset($_SESSION['cart_json']) ? $_SESSION['cart_json'] : '{}';
$cart_items = json_decode($cart_json, true);

$total_price = 0;
?>

<section style="text-align: center; padding: 4rem 0; background-color: var(--bg-warm);">
    <h1>Your Shopping Cart</h1>
</section>

<section class="container" style="margin-top: 3rem;">
    <?php if (empty($cart_items)): ?>
        <article style="text-align: center; padding: 3rem;">
            <h3>Your cart is empty.</h3>
            <p>Looks like you haven't added any eco-friendly goodies yet.</p>
            <a href="<?php echo BASE_URL; ?>/shop.php" role="button">Go to Shop</a>
        </article>
    <?php else: ?>
        <div class="overflow-auto">
            <table class="striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): 
                        $line_total = $item['price'] * $item['qty'];
                        $total_price += $line_total;
                    ?>
                    <tr>
                        <td style="width: 80px;">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" width="50" style="border-radius:4px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['qty']; ?></td>
                        <td>$<?php echo number_format($line_total, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Grand Total:</strong></td>
                        <td><strong>$<?php echo number_format($total_price, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="grid" style="margin-top: 2rem;">
            <div>
                <a href="<?php echo BASE_URL; ?>/cart-action.php?action=clear" role="button" class="secondary outline">Clear Cart</a>
            </div>
            <div style="text-align: right;">
                <a href="<?php echo BASE_URL; ?>/shop.php" role="button" class="outline">Continue Shopping</a>
                <!-- Checkout link -->
                <a href="<?php echo BASE_URL; ?>/checkout-page.php" role="button">Checkout Now</a>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>