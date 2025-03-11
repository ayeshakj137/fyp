<?php
require __DIR__ . '/../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $recipe_id = $_POST['recipe_id'];
    $rating = $_POST['rating'];

    $query = "INSERT INTO ratings_reviews (user_id, recipe_id, rating) 
              VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $recipe_id, $rating);
    mysqli_stmt_execute($stmt);
}

header("Location: view_recipe.php?id=$recipe_id");
