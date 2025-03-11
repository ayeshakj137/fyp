<?php
// pages/login.php
session_start();
include_once __DIR__ . '/../includes/config.php';
include_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        header('Location: ../index.php');
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    <div class="max-w-md mx-auto mt-20 p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border rounded" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-2 mb-4 border rounded" required>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        </form>
        <p class="mt-4">Don't have an account? <a href="signup.php" class="text-blue-500">Sign Up</a></p>
    </div>
    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>