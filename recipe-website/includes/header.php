<?php
// header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection if not already included
require_once __DIR__ . '/../includes/config.php';

// Fetch the current user's profile picture if logged in
$profile_pic = '';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $stmt = mysqli_prepare($conn, "SELECT profile_pic FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $profile_pic);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

// Ensure profile picture path is correct
$profile_pic_url = !empty($profile_pic) ? URL_ROOT . "/uploads/" . htmlspecialchars($profile_pic) : URL_ROOT . "/assets/default-avatar.png"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <header class="bg-white dark:bg-gray-800 shadow fixed w-full top-0 z-50">
        <nav class="container mx-auto p-4 flex justify-between items-center">
            <!-- Website Logo -->
            <a href="<?php echo URL_ROOT; ?>" class="text-xl font-bold"><?php echo SITE_NAME; ?></a>

            <!-- Navigation Links -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode / Light Mode Toggle -->
                <button onclick="toggleDarkMode()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                    <span id="theme-icon">🌙</span>
                </button>

                <!-- Search Link -->
                <a href="<?php echo URL_ROOT; ?>/pages/search.php" class="hover:text-blue-500">Search</a>

                <!-- Create Recipe Button (Visible when logged in) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo URL_ROOT; ?>/pages/create_recipe.php" class="hover:text-blue-500">Create Recipe</a>
                <?php endif; ?>

                <!-- Profile and Logout Links (Visible when logged in) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-2">
                        <!-- Profile Picture -->
                        <img src="<?php echo $profile_pic_url; ?>" alt="Profile Picture" class="w-8 h-8 rounded-full object-cover border border-gray-300">

                        <!-- Profile Button -->
                        <a href="<?php echo URL_ROOT; ?>/pages/profile.php" class="hover:text-blue-500">Profile</a>
                    </div>

                    <!-- Logout Button -->
                    <a href="<?php echo URL_ROOT; ?>/pages/logout.php" class="hover:text-blue-500">Logout</a>
                <?php else: ?>
                    <!-- Login / Signup Links (Visible when not logged in) -->
                    <a href="<?php echo URL_ROOT; ?>/pages/login.php" class="hover:text-blue-500">Login</a>
                    <a href="<?php echo URL_ROOT; ?>/pages/signup.php" class="hover:text-blue-500">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- JavaScript for Dark Mode Toggle -->
    <script>
        function toggleDarkMode() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const themeIcon = document.getElementById('theme-icon');
            if (localStorage.getItem('theme') === 'dark') {
                themeIcon.textContent = '☀️'; // Sun icon for light mode
            } else {
                themeIcon.textContent = '🌙'; // Moon icon for dark mode
            }
        }

        // Set initial theme and icon
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
        updateThemeIcon();
    </script>
