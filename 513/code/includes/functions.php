<?php
// includes/functions.php

/**
 * Checks if a user is logged in. If not, redirects to the login page.
 */
function check_login() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        // A fallback in case this function is called before config is loaded.
        if (!defined('BASE_URL')) {
            // This path is hardcoded but will work for this specific case.
            header("location: /513/week7/fluent-login.php"); // UPDATED
        } else {
            header("location: " . BASE_URL . "/fluent-login.php"); // UPDATED
        }
        exit;
    }
}
/**
 * Checks if a logged-in user has the 'admin' role. If not, redirects to the homepage.
 */
function check_admin() {
    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
        if (!defined('BASE_URL')) {
            header("location: /513/week7/index.php"); 
        } else {
            header("location: " . BASE_URL . "/index.php"); 
        }
        exit;
    }
}
?>

<?php
// ... (keep all the existing functions above)

/**
 * Fetches curated photos from the Pexels API.
 * @param string $query The search term for photos.
 * @param int $per_page The number of photos to fetch.
 * @return array An array of photo URLs or an empty array on failure.
 */
function get_pexels_images($query = 'sustainable lifestyle', $per_page = 6) {
    // --- IMPORTANT: Replace with YOUR actual Pexels API Key ---
    $api_key = 'rpPo7sPuMdGlHs2C9ezmICIw8b1DB9i7T2wozORzq8LW0tpNztPRT1fb';
    $api_url = "https://api.pexels.com/v1/search?query=" . urlencode($query) . "&per_page=" . $per_page;

    // --- Pexels requires the API key to be sent in the HTTP Headers ---
    $headers = [
        'Authorization: ' . $api_key
    ];

    // Use cURL to make the API request with custom headers
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set the authorization header

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get the HTTP status code
    curl_close($ch);

    // Check if the API call was successful (HTTP 200 OK)
    if ($http_code === 200) {
        $data = json_decode($response, true);
        $photo_urls = [];
        if (isset($data['photos'])) {
            foreach ($data['photos'] as $photo) {
                // We want the 'medium' size for good quality without being too large
                $photo_urls[] = $photo['src']['medium'];
            }
        }
        return $photo_urls;
    }

    // Return an empty array if the API call failed
    return [];
}
// ... (keep all existing functions above)

/**
 * Reads all products from the products.json file.
 * @return array The array of products.
 */
function get_all_products_from_json() {
    $json_file_path = ROOT_PATH . '/products.json';
    if (!file_exists($json_file_path)) {
        return [];
    }
    $json_data = file_get_contents($json_file_path);
    return json_decode($json_data, true);
}
?>

