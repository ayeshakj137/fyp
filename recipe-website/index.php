<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration and functions
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

// Debug: Check if config.php is loaded
if (!defined('URL_ROOT')) {
    die("Config file not loaded.");
}

// Fetch trending recipes (limit to 6)
$query = "SELECT recipes.*, users.name AS author, users.profile_pic AS author_profile_pic 
          FROM recipes 
          LEFT JOIN users ON recipes.user_id = users.user_id 
          ORDER BY created_at DESC 
          LIMIT 6";
$result = mysqli_query($conn, $query);

// Debug: Check if the query executed successfully
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Website - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-blue-500 text-white py-20 text-center">
        <h1 class="text-4xl font-bold">Welcome to <?php echo SITE_NAME; ?></h1>
        <p class="mt-4">Discover and share delicious recipes from around the world.</p>

        <!-- Chat with Chef Bot Button -->
        <div class="mt-6">
            <a href="pages/chef_bot.php" class="bg-white text-blue-500 px-6 py-2 rounded-full font-semibold">🤖Chat with Chef Bot for Quick Recipes</a>
        </div>
    </section>

    <!-- Trending Recipes -->
    <section class="p-6">
        <h2 class="text-2xl font-bold">Trending Recipes</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow-lg">
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>" 
                             class="w-full h-48 object-cover rounded">
                        <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <div class="flex items-center mt-2">
                            <?php if (!empty($row['author_profile_pic'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['author_profile_pic']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['author']); ?>" 
                                     class="w-8 h-8 rounded-full mr-2">
                            <?php endif; ?>
                            <p class="text-sm">By <?php echo htmlspecialchars($row['author']); ?></p>
                        </div>
                        <p class="text-sm mt-2"><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="pages/recipe.php?id=<?php echo $row['recipe_id']; ?>" class="text-blue-500 mt-4 inline-block">View Recipe</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">No recipes found.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>