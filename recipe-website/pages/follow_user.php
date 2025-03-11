<?php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect(URL_ROOT . '/pages/login.php');
}

$user_id = $_SESSION['user_id'];
$following_id = $_POST['following_id'] ?? null; // Use 'following_id' instead of 'followed_id'

if (!$following_id || $user_id == $following_id) {
    redirect(URL_ROOT);
}

// Check if the user already follows
$checkQuery = "SELECT * FROM followers WHERE follower_id = ? AND following_id = ?";
$stmt = mysqli_prepare($conn, $checkQuery);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $following_id);
mysqli_stmt_execute($stmt);
$checkResult = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($checkResult)) {
    // Unfollow
    $deleteQuery = "DELETE FROM followers WHERE follower_id = ? AND following_id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $following_id);
    mysqli_stmt_execute($stmt);
} else {
    // Follow
    $insertQuery = "INSERT INTO followers (follower_id, following_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $following_id);
    mysqli_stmt_execute($stmt);
}

redirect($_SERVER['HTTP_REFERER'] ?? URL_ROOT);
?>