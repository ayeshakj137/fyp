<?php
session_start();
require __DIR__ . '/../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$recipe_id = intval($_POST['recipe_id']);

// Check if the recipe is already saved
$checkStmt = mysqli_prepare($conn, "SELECT * FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
mysqli_stmt_bind_param($checkStmt, "ii", $user_id, $recipe_id);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($result) > 0) {
    // If the recipe is already saved, remove it (unsave)
    $deleteStmt = mysqli_prepare($conn, "DELETE FROM saved_recipes WHERE user_id = ? AND recipe_id = ?");
    mysqli_stmt_bind_param($deleteStmt, "ii", $user_id, $recipe_id);
    if (mysqli_stmt_execute($deleteStmt)) {
        echo "Recipe unsaved";
    } else {
        echo "Error unsaving recipe";
    }
} else {
    // If not saved, insert the recipe into saved_recipes
    $stmt = mysqli_prepare($conn, "INSERT INTO saved_recipes (user_id, recipe_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $recipe_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Recipe saved";
    } else {
        echo "Error saving recipe";
    }
}

?>
