<?php
require __DIR__ . '/../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $recipe_id = $_POST['recipe_id'];
    $comment = $_POST['comment'];

    $query = "INSERT INTO comments (user_id, recipe_id, comment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $recipe_id, $comment);
    mysqli_stmt_execute($stmt);
}

header("Location: view_recipe.php?id=$recipe_id");
