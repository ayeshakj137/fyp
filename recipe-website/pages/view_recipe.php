<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Ensure the recipe ID is provided
if (!isset($_GET['id'])) {
    redirect(URL_ROOT);
}

$recipe_id = sanitizeInput($_GET['id']);

// Fetch the recipe details
$stmt = mysqli_prepare($conn, "SELECT recipes.*, users.name AS author FROM recipes 
    LEFT JOIN users ON recipes.user_id = users.user_id 
    WHERE recipes.recipe_id = ?");
mysqli_stmt_bind_param($stmt, "i", $recipe_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$recipe = mysqli_fetch_assoc($result);

if (!$recipe) {
    redirect(URL_ROOT);
}

// Fetch average rating and total reviews
$ratingQuery = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM ratings_reviews WHERE recipe_id = ?";
$stmt = mysqli_prepare($conn, $ratingQuery);
mysqli_stmt_bind_param($stmt, "i", $recipe_id);
mysqli_stmt_execute($stmt);
$ratingResult = mysqli_stmt_get_result($stmt);
$ratingData = mysqli_fetch_assoc($ratingResult);
$avg_rating = round($ratingData['avg_rating'], 1);
$total_reviews = $ratingData['total_reviews'];

// Fetch comments
$commentQuery = "SELECT comments.comment, users.name, comments.created_at FROM comments 
    JOIN users ON comments.user_id = users.user_id 
    WHERE recipe_id = ? ORDER BY comments.created_at DESC";
$stmt = mysqli_prepare($conn, $commentQuery);
mysqli_stmt_bind_param($stmt, "i", $recipe_id);
mysqli_stmt_execute($stmt);
$commentResult = mysqli_stmt_get_result($stmt);
$comments = mysqli_fetch_all($commentResult, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p class="text-sm">By <?php echo htmlspecialchars($recipe['author']); ?></p>

        <!-- Display Average Rating -->
        <div class="mt-4">
            <p class="text-lg font-semibold">Rating: <?php echo $avg_rating ? "$avg_rating / 5" : "Not rated yet"; ?> (<?php echo $total_reviews; ?> reviews)</p>
        </div>

        <!-- Recipe Image -->
        <?php if ($recipe['image']): ?>
            <img src="../uploads/<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="w-full h-64 object-cover rounded mt-4">
        <?php endif; ?>

        <!-- Recipe Description -->
        <p class="mt-4"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>

        <!-- Ingredients -->
        <h2 class="text-xl font-bold mt-6">Ingredients</h2>
        <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>

        <!-- Steps -->
        <h2 class="text-xl font-bold mt-6">Steps</h2>
        <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($recipe['steps'])); ?></p>

        <!-- Rating Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="rate_recipe.php" method="POST" class="mt-6">
                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                <label class="block text-lg font-semibold">Rate this recipe:</label>
                <select name="rating" class="border p-2 mt-2">
                    <option value="1">1 Star</option>
                    <option value="2">2 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="5">5 Stars</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Submit</button>
            </form>
        <?php endif; ?>

        <!-- Comments Section -->
        <h2 class="text-xl font-bold mt-6">Comments</h2>
        <div class="mt-4">
            <?php foreach ($comments as $comment): ?>
                <div class="bg-gray-200 text-black p-4 rounded mb-2">
                    <p><strong><?php echo htmlspecialchars($comment['name']); ?></strong> - <?php echo $comment['created_at']; ?></p>
                    <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Comment Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="comment_recipe.php" method="POST" class="mt-6">
                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                <textarea name="comment" class="border w-full p-2" placeholder="Write a comment..." required></textarea>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Post Comment</button>
            </form>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
