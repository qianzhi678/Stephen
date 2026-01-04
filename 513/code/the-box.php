<?php
// the-box.php (JSON-DRIVEN VERSION)
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php'; // Contains get_all_products_from_json()
require_once ROOT_PATH . '/includes/header.php';

// --- DATA FETCHING LOGIC ---

// 1. Fetch all product data from the JSON file into an array.
// This is our primary source of product information now.
$all_products = get_all_products_from_json();

// 2. Find the LATEST box from the DATABASE.
// We still use the database to manage which box is the current one.
$stmt_box = $pdo->query("SELECT * FROM boxes ORDER BY box_id DESC LIMIT 1");
$active_box = $stmt_box->fetch(PDO::FETCH_ASSOC);

$products_in_box = [];
if ($active_box && !empty($all_products)) {
    // 3. Get the IDs of products that BELONG to this box from the DATABASE link table.
    $stmt_product_ids = $pdo->prepare("SELECT product_id FROM box_products WHERE box_id = :box_id");
    $stmt_product_ids->execute(['box_id' => $active_box['box_id']]);
    $product_ids_in_box = $stmt_product_ids->fetchAll(PDO::FETCH_COLUMN, 0);

    // 4. FILTER the main JSON product list to get only the products for this box.
    // This is the core of the new logic.
    foreach ($all_products as $product) {
        if (in_array($product['id'], $product_ids_in_box)) {
            $products_in_box[] = $product;
        }
    }
}
?>

<!-- --- HTML DISPLAY --- -->

<section class="container" style="text-align: center; padding-top: 4rem;">
    <?php if ($active_box): ?>
        <h2>This Quarter's Theme: <?php echo htmlspecialchars($active_box['theme']); ?></h2>
        <h1 style="font-size: 3rem;"><?php echo htmlspecialchars($active_box['name']); ?></h1>
        <p>A curated selection of high-quality, sustainable products delivered to your door.</p>
    <?php else: ?>
        <h1>Our Next Box is Coming Soon!</h1>
    <?php endif; ?>
</section>

<?php if (!empty($products_in_box)): ?>
<section class="container" style="margin-top: 3rem;">
    <h3 style="text-align: center;">What's Inside?</h3>
    <div class="grid">
        <?php foreach ($products_in_box as $product): ?>
            <article>
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 250px; object-fit: cover;">
                <header><strong><?php echo htmlspecialchars($product['name']); ?></strong></header>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                
                <!-- NEW: View Details Button -->
                <a href="<?php echo BASE_URL; ?>/view-product.php?id=<?php echo $product['id']; ?>" 
                   role="button" 
                   class="outline">
                   View Details
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<section class="container" style="text-align: center; padding: 3rem 0;">
    <a href="<?php echo BASE_URL; ?>/subscribe.php" role="button" class="contrast">Subscribe Now & Get This Box!</a>
</section>
<?php endif; ?>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>