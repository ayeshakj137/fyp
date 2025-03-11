<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php'; // Ensure this is included

// Ensure the recipe ID is provided
if (!isset($_GET['id'])) {
    redirect(URL_ROOT);
}

$recipe_id = sanitizeInput($_GET['id']); // Now this function will be recognized
$user_id = $_SESSION['user_id'] ?? null;
?>
