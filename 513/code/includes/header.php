<?php
// includes/header.php (FINAL MERGED VERSION)
if (session_status() == PHP_SESSION_NONE) { session_start(); }
@require_once __DIR__ . '/../config/database.php';

// --- CART COUNT LOGIC ---
$header_cart_json = isset($_SESSION['cart_json']) ? $_SESSION['cart_json'] : '{}';
$header_cart_array = json_decode($header_cart_json, true);
$header_cart_count = 0;
if (is_array($header_cart_array)) {
    foreach ($header_cart_array as $item) {
        $header_cart_count += isset($item['qty']) ? $item['qty'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Conscious Home Box</title>
    
    <!-- 1. Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <!-- 2. CSS Libraries -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/pico.min.css">
    
    <!-- 3. Custom Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

    <!-- 4. Theme Toggle Script (Runs immediately to prevent flash) -->
    <script>
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
</head>
<body>
    <nav class="container-fluid">
        <ul>
            <li>
                <a href="<?php echo BASE_URL; ?>/" style="padding: 0.5rem 1rem;">
                    <img src="<?php echo BASE_URL; ?>/assets/images/logo1.png" alt="The Conscious Home Box Logo" style="height: 120px; vertical-align: middle;">
                </a>
            </li>
        </ul>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/the-box.php">The Box</a></li>
            <li><a href="<?php echo BASE_URL; ?>/shop.php">Shop</a></li>
            <li><a href="<?php echo BASE_URL; ?>/past-boxes.php">Past Boxes</a></li>
            
            <!-- Cart Button -->
            <li>
                <a href="<?php echo BASE_URL; ?>/cart.php" role="button" class="outline">
                    Cart 
                    <?php if ($header_cart_count > 0): ?>
                        (<?php echo $header_cart_count; ?>)
                    <?php endif; ?>
                </a>
            </li>

            <li><a href="<?php echo BASE_URL; ?>/subscribe.php">Subscribe</a></li>

            <!-- Link to WordPress Subscribers List Page -->
            <li><a href="http://121.196.229.71/subscribers-list/">Community</a></li>
            
            <!-- Link to PHP Native Forum -->
            <li><a href="<?php echo BASE_URL; ?>/forum.php">Forum</a></li>

            <!-- Careers Link -->
            <li><a href="<?php echo BASE_URL; ?>/recruitment.php">Careers</a></li>
            
            <!-- Dark Mode Toggle Button -->
            <li>
                <button id="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode" style="background:transparent; border:none; padding:0; margin-left:10px; font-size:1.2rem; cursor:pointer;">🌙</button>
            </li>

            <!-- User & Admin Links -->
            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                
                <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === 'admin'): ?>
                    <li><a href="<?php echo BASE_URL; ?>/admin/" role="button" class="contrast">Admin Panel</a></li>
                <?php endif; ?>
                
                <li><a href="<?php echo BASE_URL; ?>/my-account.php">My Account</a></li>
                <li><a href="<?php echo BASE_URL; ?>/auth/logout.php">Logout</a></li>

            <?php else: ?>
                <li><a href="<?php echo BASE_URL; ?>/fluent-login.php">Login</a></li>
                <li><a href="http://121.196.229.71/register/" role="button">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Theme Toggle JS Logic -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const button = document.getElementById('theme-toggle');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                button.innerHTML = '🌙'; 
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                button.innerHTML = '☀️'; 
            }
        }
        
        // Set correct icon on load
        if (localStorage.getItem('theme') === 'dark') {
            document.getElementById('theme-toggle').innerHTML = '☀️';
        }
    </script>

    <main class="container">