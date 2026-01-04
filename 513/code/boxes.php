<?php
// admin/boxes.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

check_login();
check_admin();

// === HANDLE FORM SUBMISSION TO ADD/UPDATE A BOX ===
if (isset($_POST['submit_box'])) {
    $box_name = trim($_POST['box_name']);
    $box_theme = trim($_POST['box_theme']);
    $box_id = $_POST['box_id'] ?? null;

    if (!empty($box_name)) {
        if ($box_id) {
            // Update box info
            $sql = "UPDATE boxes SET name = :name, theme = :theme WHERE box_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['name' => $box_name, 'theme' => $box_theme, 'id' => $box_id]);
        } else {
            // Create new box
            $sql = "INSERT INTO boxes (name, theme) VALUES (:name, :theme)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['name' => $box_name, 'theme' => $box_theme]);
        }
    }
    header("Location: /admin/boxes.php");
    exit;
}

// === HANDLE ADDING/REMOVING PRODUCTS FROM A BOX ===
if (isset($_POST['update_box_products'])) {
    $box_id = $_POST['box_id'];
    $selected_products = $_POST['products'] ?? [];

    // First, remove all existing products for this box to avoid duplicates
    $sql_delete = "DELETE FROM box_products WHERE box_id = :box_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute(['box_id' => $box_id]);

    // Now, insert the newly selected products
    if (!empty($selected_products)) {
        $sql_insert = "INSERT INTO box_products (box_id, product_id) VALUES (:box_id, :product_id)";
        $stmt_insert = $pdo->prepare($sql_insert);
        foreach ($selected_products as $product_id) {
            $stmt_insert->execute(['box_id' => $box_id, 'product_id' => $product_id]);
        }
    }
    header("Location: /admin/boxes.php?view=" . $box_id);
    exit;
}

// Fetch all boxes
$boxes = $pdo->query("SELECT * FROM boxes ORDER BY box_id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <h3>Admin Menu</h3>
        <nav>
            <ul>
                <li><a href="/admin/index.php">Dashboard</a></li>
                <li><a href="/admin/products.php">Manage Products</a></li>
                <li><a href="/admin/boxes.php" class="active">Manage Boxes</a></li>
                <li><a href="#">Manage Users</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <h2>Manage Subscription Boxes</h2>
        
        <div class="grid">
            <!-- Left column for Adding Boxes and Listing them -->
            <section>
                <article>
                    <header><h4>Add New Box</h4></header>
                    <form action="boxes.php" method="post">
                        <label for="box_name">Box Name</label>
                        <input type="text" name="box_name" required>
                        <label for="box_theme">Theme (e.g., Kitchen, Bathroom)</label>
                        <input type="text" name="box_theme">
                        <button type="submit" name="submit_box">Create Box</button>
                    </form>
                </article>

                <article>
                    <header><h4>All Boxes</h4></header>
                    <table>
                        <thead><tr><th>Name</th><th>Theme</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($boxes as $box): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($box['name']); ?></td>
                                <td><?php echo htmlspecialchars($box['theme']); ?></td>
                                <td><a href="boxes.php?view=<?php echo $box['box_id']; ?>">Manage Products</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </article>
            </section>

            <!-- Right column for managing products OF a selected box -->
            <section>
                <?php if (isset($_GET['view'])):
                    $view_id = $_GET['view'];
                    // Get the box details
                    $stmt_box = $pdo->prepare("SELECT * FROM boxes WHERE box_id = ?");
                    $stmt_box->execute([$view_id]);
                    $selected_box = $stmt_box->fetch();

                    // Get all available products
                    $all_products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

                    // Get product IDs already in this box
                    $stmt_box_prods = $pdo->prepare("SELECT product_id FROM box_products WHERE box_id = ?");
                    $stmt_box_prods->execute([$view_id]);
                    $product_ids_in_box = $stmt_box_prods->fetchAll(PDO::FETCH_COLUMN, 0);
                ?>
                <article>
                    <header><h4>Manage Products for: <?php echo htmlspecialchars($selected_box['name']); ?></h4></header>
                    <form action="boxes.php" method="post">
                        <input type="hidden" name="box_id" value="<?php echo $view_id; ?>">
                        <fieldset>
                            <legend>Select Products to Include:</legend>
                            <?php foreach ($all_products as $product): ?>
                                <label for="product_<?php echo $product['product_id']; ?>">
                                    <input 
                                        type="checkbox" 
                                        id="product_<?php echo $product['product_id']; ?>" 
                                        name="products[]" 
                                        value="<?php echo $product['product_id']; ?>"
                                        <?php if (in_array($product['product_id'], $product_ids_in_box)) echo 'checked'; ?>
                                    >
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                        <button type="submit" name="update_box_products">Save Changes</button>
                    </form>
                </article>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>

</body>
</html>