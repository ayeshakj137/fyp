<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    redirect(URL_ROOT . '/pages/login.php');
}

// Fetch the recipe ID from the POST data
if (!isset($_POST['recipe_id'])) {
    die("Recipe ID not provided.");
}
$recipe_id = sanitizeInput($_POST['recipe_id']);
$user_id = $_SESSION['user_id'];

// Delete the recipe
$stmt = mysqli_prepare($conn, "DELETE FROM recipes WHERE recipe_id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $recipe_id, $user_id);
if (mysqli_stmt_execute($stmt)) {
    redirect(URL_ROOT . '/pages/profile.php');
} else {
    die("Error deleting recipe.");
}
?>