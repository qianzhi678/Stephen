<?php
// past-boxes.php (JSON-DRIVEN VERSION)
require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/header.php';

// --- DATA FETCHING LOGIC ---

// 1. Fetch ALL product data from the JSON file just once.
$all_products = get_all_products_from_json();

// 2. Find the ID of the most recent box from the DATABASE.
$latest_box_id_stmt = $pdo->query("SELECT box_id FROM boxes ORDER BY box_id DESC LIMIT 1");
$latest_box_id = $latest_box_id_stmt->fetchColumn();

// 3. Fetch all PAST boxes from the DATABASE (all except the latest one).
if ($latest_box_id) {
    $stmt_past_boxes = $pdo->prepare("SELECT * FROM boxes WHERE box_id != :latest_id ORDER BY box_id DESC");
    $stmt_past_boxes->execute(['latest_id' => $latest_box_id]);
    $past_boxes = $stmt_past_boxes->fetchAll(PDO::FETCH_ASSOC);
} else {
    $past_boxes = $pdo->query("SELECT * FROM boxes ORDER BY box_id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- --- HTML DISPLAY --- -->

<section class="container" style="text-align: center; padding-top: 4rem;">
    <h1 style="font-size: 3rem;">Past Boxes Archive</h1>
    <p>Take a look at the amazing collections we've delivered in the past!</p>
</section>

<section class="container" style="margin-top: 3rem;">
    <?php if (empty($past_boxes)): ?>
        <article style="text-align: center;">
            <p>Our journey is just beginning! More past boxes will appear here soon.</p>
        </article>
    <?php else: ?>
        <?php foreach ($past_boxes as $box): ?>
            <article style="margin-bottom: 3rem;">
                <header>
                    <h4 style="margin-bottom: 1rem;"><?php echo htmlspecialchars($box['name']); ?> (<?php echo htmlspecialchars($box['theme']); ?> Theme)</h4>
                </header>
                
                <?php
                // 4. Get the IDs of products that belonged to THIS specific box.
                $stmt_product_ids = $pdo->prepare("SELECT product_id FROM box_products WHERE box_id = :box_id");
                $stmt_product_ids->execute(['box_id' => $box['box_id']]);
                $product_ids_in_this_box = $stmt_product_ids->fetchAll(PDO::FETCH_COLUMN, 0);
                
                // 5. FILTER the main JSON product list to find products for THIS box.
                $products_in_this_box = [];
                if (!empty($all_products)) {
                    foreach ($all_products as $product) {
                        if (in_array($product['id'], $product_ids_in_this_box)) {
                            $products_in_this_box[] = $product;
                        }
                    }
                }
                ?>

                <!-- Display the products found for this box -->
                <?php if (!empty($products_in_this_box)): ?>
                    <div class="grid">
                        <?php foreach ($products_in_this_box as $product): ?>
                            <div class="past-box-item" style="text-align: center;">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: var(--border-radius);">
                                <small><?php echo htmlspecialchars($product['name']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php
require_once ROOT_PATH . '/includes/footer.php';
?>