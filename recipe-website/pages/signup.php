<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Debug: Check if config.php is loaded
if (!defined('URL_ROOT')) {
    die("Config file not loaded.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);

    // Check if the email already exists
    $stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Email already exists
        $error = "This email is already registered. Please use a different email.";
    } else {
        // Insert the new user
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to login page after successful signup
            redirect(URL_ROOT . '/pages/login.php');
        } else {
            $error = "Error registering user. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold">Sign Up</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <form action="signup.php" method="post" class="mt-4">
            <label for="name" class="block">Name:</label>
            <input type="text" name="name" required class="p-2 border border-gray-300 rounded">

            <label for="email" class="block mt-4">Email:</label>
            <input type="email" name="email" required class="p-2 border border-gray-300 rounded">

            <label for="password" class="block mt-4">Password:</label>
            <input type="password" name="password" required class="p-2 border border-gray-300 rounded">

            <button type="submit" class="bg-blue-500 text-white p-2 rounded mt-4">Sign Up</button>
        </form>
        <p class="mt-4">Already have an account? <a href="<?php echo URL_ROOT; ?>/pages/login.php" class="text-blue-500">Login here</a>.</p>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>