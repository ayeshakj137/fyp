<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Correct the path to config.php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Debug: Check if config.php is loaded
if (!defined('URL_ROOT')) {
    die("Config file not loaded.");
}

// Fetch categories for dropdown
$category_query = "SELECT * FROM recipe_categories";
$category_result = mysqli_query($conn, $category_query);

// Handle search query
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Number of recipes per page
$offset = ($page - 1) * $limit;

// Build query
$query = "SELECT recipes.*, users.name AS author 
          FROM recipes 
          LEFT JOIN users ON recipes.user_id = users.user_id 
          WHERE recipes.title LIKE ?";

// Add category filter if selected
if ($category > 0) {
    $query .= " AND recipes.category_id = ?";
}

$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $query);
$searchTerm = "%$search%";

// Bind parameters dynamically
if ($category > 0) {
    mysqli_stmt_bind_param($stmt, "siii", $searchTerm, $category, $limit, $offset);
} else {
    mysqli_stmt_bind_param($stmt, "sii", $searchTerm, $limit, $offset);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get total number of recipes for pagination
$total_query = "SELECT COUNT(*) FROM recipes WHERE title LIKE ?";
if ($category > 0) {
    $total_query .= " AND category_id = ?";
}

$stmt_total = mysqli_prepare($conn, $total_query);
if ($category > 0) {
    mysqli_stmt_bind_param($stmt_total, "si", $searchTerm, $category);
} else {
    mysqli_stmt_bind_param($stmt_total, "s", $searchTerm);
}

mysqli_stmt_execute($stmt_total);
$total_result = mysqli_stmt_get_result($stmt_total);
$total_recipes = mysqli_fetch_column($total_result, 0);
$total_pages = ceil($total_recipes / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Recipes - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold">Search Recipes</h1>

        <!-- Search Form with Category Filter -->
        <form action="search.php" method="get" class="mt-4 flex flex-col md:flex-row gap-2">
            <input type="text" name="search" placeholder="Search recipes..." value="<?php echo htmlspecialchars($search); ?>" class="p-2 border border-gray-300 rounded flex-grow">
            
            <select name="category" class="p-2 border border-gray-300 rounded">
                <option value="0">All Categories</option>
                <?php while ($cat = mysqli_fetch_assoc($category_result)): ?>
                    <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="bg-blue-500 text-white p-2 rounded">Search</button>
        </form>

        <!-- Display Results -->
        <?php if (!empty($search) && mysqli_num_rows($result) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-gray-200 dark:bg-gray-700 p-4 rounded shadow-lg">
                        <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>" 
                             class="w-full h-40 object-cover rounded">
                        <h4 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($row['title']); ?></h4>
                        <p class="text-sm">By <?php echo htmlspecialchars($row['author']); ?></p>
                        <p class="text-sm"><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="../pages/recipe.php?id=<?php echo $row['recipe_id']; ?>" class="text-blue-500">View Recipe</a>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <?php if ($page > 1): ?>
                    <a href="search.php?search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&page=<?php echo $page - 1; ?>" class="bg-blue-500 text-white p-2 rounded">Previous</a>
                <?php endif; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="search.php?search=<?php echo urlencode($search); ?>&category=<?php echo $category; ?>&page=<?php echo $page + 1; ?>" class="bg-blue-500 text-white p-2 rounded">Next</a>
                <?php endif; ?>
            </div>
        <?php elseif (!empty($search)): ?>
            <p class="text-gray-500 mt-4">No recipes found.</p>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
