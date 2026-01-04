<?php
// config/database.php

/* -- INFINITYFREE DATABASE CREDENTIALS -- */
// !! IMPORTANT !! Fill these with your actual credentials from InfinityFree cPanel
define('DB_SERVER', 'sql108.infinityfree.com');      // 
define('DB_USERNAME', 'if0_39945745');                // 
define('DB_PASSWORD', '0tyiCX55f806dxj');                // 
define('DB_NAME', 'if0_39945745_consciousbox');   //

// === GLOBAL PATH CONFIGURATION ===
define('BASE_URL', '/513/week7');
define('ROOT_PATH', __DIR__ . '/..');

// === DATABASE CONNECTION ===
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    die("FATAL DATABASE ERROR: Could not connect. Check credentials. Error: " . $e->getMessage());
}
?>