<?php
// view-product.php - Displays a single product's details
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

// --- DATA FETCHING LOGIC ---

// 1. Get the product ID from the URL query string (?id=)
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product_found = null;

if ($product_id > 0) {
    // 2. Load all products from our JSON file
    $all_products = get_all_products_from_json();
    
    // 3. Find the specific product that matches the ID
    foreach ($all_products as $p) {
        if ($p['id'] === $product_id) {
            $product_found = $p;
            break; // Stop the loop once we find it
        }
    }
}

?>

<!-- --- HTML DISPLAY --- -->
<section class="container" style="padding-top: 4rem;">

    <?php if ($product_found): // If a product was successfully found ?>
        
        <div class="grid" style="align-items: center;">
            <!-- Left Column: Image -->
            <div data-aos="fade-right">
                <figure style="margin: 0;">
                    <img src="<?php echo htmlspecialchars($product_found['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product_found['name']); ?>" 
                         style="border-radius: var(--border-radius); width: 100%;">
                </figure>
            </div>

            <!-- Right Column: Details -->
            <div data-aos="fade-left" data-aos-delay="200">
                <h1 style="font-size: 3rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($product_found['name']); ?></h1>
                
                <p style="font-size: 1.5rem; color: var(--primary); margin-bottom: 2rem;">
                    $<?php echo number_format($product_found['price'], 2); ?>
                </p>
                
                <p style="color: var(--text-light); line-height: 1.8;">
                    <?php echo htmlspecialchars($product_found['description']); ?>
                </p>

                <form action="<?php echo BASE_URL; ?>/cart-action.php" method="post" style="margin-top: 2rem;">
                    <input type="hidden" name="product_id" value="<?php echo $product_found['id']; ?>">
                    <button type="submit" class="contrast">Add to Cart</button>
                </form>
            </div>
        </div>

    <?php else: // If no product was found with that ID ?>
    
        <article style="text-align: center;">
            <h2>Product Not Found</h2>
            <p>Sorry, we couldn't find the product you're looking for.</p>
            <a href="<?php echo BASE_URL; ?>/shop.php" role="button">Back to Shop</a>
        </article>

    <?php endif; ?>

</section>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>