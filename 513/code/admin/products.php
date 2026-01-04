<?php
// admin/products.php (JSON-DRIVEN VERSION)
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . '/includes/functions.php';

check_login();
check_admin();

// Define the path to your JSON file
$json_file_path = ROOT_PATH . '/products.json';

// Function to read products from JSON
function get_products_from_json($path) {
    if (!file_exists($path)) return [];
    $json_data = file_get_contents($path);
    return json_decode($json_data, true);
}

// Function to save products to JSON
function save_products_to_json($path, $products) {
    // Use JSON_PRETTY_PRINT for readability
    $json_data = json_encode($products, JSON_PRETTY_PRINT);
    file_put_contents($path, $json_data);
}

// Get all current products
$products = get_products_from_json($json_file_path);

// --- Handle Form Submissions (Create, Update, Delete) ---

// Handle DELETE
if (isset($_GET['delete'])) {
    $id_to_delete = (int)$_GET['delete'];
    // Filter out the product to be deleted
    $products = array_filter($products, function($p) use ($id_to_delete) {
        return $p['id'] !== $id_to_delete;
    });
    save_products_to_json($json_file_path, array_values($products)); // Re-index array
    header("Location: " . BASE_URL . "/admin/products.php");
    exit;
}

// Handle CREATE or UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $edit_id = !empty($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;

    if ($edit_id) { // UPDATE
        foreach ($products as &$product) {
            if ($product['id'] === $edit_id) {
                $product['name'] = $_POST['name'];
                $product['description'] = $_POST['description'];
                $product['price'] = (float)$_POST['price'];
                $product['image_url'] = $_POST['image_url'];
                break;
            }
        }
    } else { // CREATE
        // Find the highest existing ID to create the next one
        $new_id = 0;
        foreach ($products as $p) {
            if ($p['id'] > $new_id) $new_id = $p['id'];
        }
        $new_id++;
        
        $new_product = [
            'id' => $new_id,
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => (float)$_POST['price'],
            'image_url' => $_POST['image_url']
        ];
        $products[] = $new_product;
    }
    
    save_products_to_json($json_file_path, $products);
    header("Location: " . BASE_URL . "/admin/products.php");
    exit;
}


// Handle EDIT (Pre-fill form)
$product_to_edit = null;
if (isset($_GET['edit'])) {
    $id_to_edit = (int)$_GET['edit'];
    foreach ($products as $p) {
        if ($p['id'] === $id_to_edit) {
            $product_to_edit = $p;
            break;
        }
    }
}


require_once ROOT_PATH . '/includes/header.php';
?>

<div class="admin-wrapper">
<aside class="admin-sidebar">
    <h3>Admin Menu</h3>
    <nav>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/admin/index.php">Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/products.php" class="active">Manage Products</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/boxes.php">Manage Boxes</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/manage-users.php">Manage Users</a></li>
        </ul>
    </nav>
</aside>
    <main class="admin-content">
        <h2>Manage Products (from JSON)</h2>
        
        <article>
            <header><h4><?php echo $product_to_edit ? 'Edit Product' : 'Add New Product'; ?></h4></header>
            <form action="<?php echo BASE_URL; ?>/admin/products.php" method="post">
                <input type="hidden" name="edit_id" value="<?php echo $product_to_edit['id'] ?? ''; ?>">
                
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product_to_edit['name'] ?? ''); ?>" required>
                
                <div class="grid">
                    <div>
                        <label>Price ($)</label>
                        <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product_to_edit['price'] ?? ''); ?>">
                    </div>
                    <div>
                        <label>Image URL</label>
                        <input type="text" name="image_url" value="<?php echo htmlspecialchars($product_to_edit['image_url'] ?? ''); ?>">
                    </div>
                </div>

                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($product_to_edit['description'] ?? ''); ?></textarea>
                
                <button type="submit"><?php echo $product_to_edit ? 'Update Product' : 'Add Product'; ?></button>
            </form>
        </article>

        <div class="products-table">
            <h4>All Products (from products.json)</h4>
            <table>
                <thead><tr><th>Image</th><th>Name</th><th>Price</th><th>Description</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($product['image_url']); ?>" width="60"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td>
                            <a href="?edit=<?php echo $product['id']; ?>">Edit</a> | 
                            <a href="?delete=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>