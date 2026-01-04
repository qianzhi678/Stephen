<?php
// cart-action.php (JSON-DRIVEN VERSION)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once ROOT_PATH . '/includes/functions.php'; // We need the JSON reader function

// Get current cart from Session
$cart_json = isset($_SESSION['cart_json']) ? $_SESSION['cart_json'] : '{}';
$cart_array = json_decode($cart_json, true);
if (!is_array($cart_array)) { $cart_array = []; }

// Handle "Add to Cart"
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    if (isset($cart_array[$product_id])) {
        $cart_array[$product_id]['qty']++;
    } else {
        // Fetch ALL products from JSON
        $all_products = get_all_products_from_json();
        $product_to_add = null;
        
        // Find the specific product by ID
        foreach ($all_products as $p) {
            if ($p['id'] === $product_id) {
                $product_to_add = $p;
                break;
            }
        }

        if ($product_to_add) {
            $cart_array[$product_id] = [
                'id'    => $product_to_add['id'],
                'name'  => $product_to_add['name'],
                'price' => (float)$product_to_add['price'],
                'image' => $product_to_add['image_url'],
                'qty'   => 1
            ];
        }
    }
}

// ... (Clear cart logic remains the same) ...
if (isset($_GET['action']) && $_GET['action'] == 'clear') { $cart_array = []; }

// Save back to Session
$_SESSION['cart_json'] = json_encode($cart_array);

// Redirect back
header("Location: " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/shop.php'));
exit;
?>