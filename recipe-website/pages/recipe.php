<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Ensure the recipe ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(URL_ROOT);
}

$recipe_id = sanitizeInput($_GET['id']);
$user_id = $_SESSION['user_id'] ?? null;

// Fetch the recipe details
$stmt = $conn->prepare("SELECT recipes.*, users.name AS author, users.user_id AS author_id FROM recipes 
    LEFT JOIN users ON recipes.user_id = users.user_id 
    WHERE recipes.recipe_id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();

if (!$recipe) {
    redirect(URL_ROOT);
}

// Fetch average rating and total reviews
$ratingQuery = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM ratings_reviews WHERE recipe_id = ?";
$stmt = $conn->prepare($ratingQuery);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$ratingResult = $stmt->get_result();
$ratingData = $ratingResult->fetch_assoc();
$avg_rating = round($ratingData['avg_rating'], 1) ?: 'Not rated yet';
$total_reviews = $ratingData['total_reviews'];

// Fetch comments
$commentQuery = "SELECT comments.comment, users.name, comments.created_at FROM comments 
    JOIN users ON comments.user_id = users.user_id 
    WHERE recipe_id = ? ORDER BY comments.created_at DESC";
$stmt = $conn->prepare($commentQuery);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$commentResult = $stmt->get_result();
$comments = $commentResult->fetch_all(MYSQLI_ASSOC);

// Check if the user has already saved this recipe
$saved = false;
if ($user_id) {
    $savedQuery = "SELECT * FROM saved_recipes WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($savedQuery);
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $savedResult = $stmt->get_result();
    $saved = $savedResult->fetch_assoc() ? true : false;
}

// Check if the current user is following the recipe author
$is_following = false;
if ($user_id && $recipe['author_id']) {
    $followQuery = "SELECT * FROM followers WHERE follower_id = ? AND following_id = ?";
    $stmt = $conn->prepare($followQuery);
    $stmt->bind_param("ii", $user_id, $recipe['author_id']);
    $stmt->execute();
    $followResult = $stmt->get_result();
    $is_following = $followResult->fetch_assoc() ? true : false;
}
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

        <!-- Report Recipe Button -->
        <?php if ($user_id): ?>
            <button onclick="document.getElementById('reportModal').classList.remove('hidden')" 
                    class="bg-red-500 text-white px-4 py-2 rounded mt-4">
                Report Recipe
            </button>
        <?php endif; ?>

        <!-- Report Recipe Modal -->
        <div id="reportModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h2 class="text-xl font-bold mb-4">Report Recipe</h2>
                <form action="report_recipe.php" method="POST">
                    <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                    <label class="block font-semibold">Reason for Reporting:</label>
                    <textarea name="reason" class="border p-2 w-full mt-2" required></textarea>
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Submit Report</button>
                    <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')" 
                            class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
                        Cancel
                    </button>
                </form>
            </div>
        </div>

        <!-- Follow/Unfollow Button -->
        <?php if ($user_id && $recipe['author_id'] && $user_id != $recipe['author_id']): ?>
            <form action="follow_user.php" method="POST" class="mt-4">
                <input type="hidden" name="following_id" value="<?php echo $recipe['author_id']; ?>">
                <button type="submit" class="bg-<?php echo $is_following ? 'red' : 'blue'; ?>-500 text-white px-4 py-2 rounded">
                    <?php echo $is_following ? "Unfollow" : "Follow"; ?> <?php echo htmlspecialchars($recipe['author']); ?>
                </button>
            </form>
        <?php endif; ?>

        <!-- Display Average Rating -->
        <div class="mt-4">
            <p class="text-lg font-semibold">Rating: <?php echo $avg_rating; ?> (<?php echo $total_reviews; ?> reviews)</p>
        </div>

        <!-- Recipe Image -->
        <?php if (!empty($recipe['image'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="w-full h-64 object-cover rounded mt-4">
        <?php endif; ?>

        <!-- Recipe Video -->
        <?php if (!empty($recipe['video'])): ?>
            <video controls class="w-full h-64 mt-4 rounded">
                <source src="../uploads/<?php echo htmlspecialchars($recipe['video']); ?>" type="video/mp4">
                <source src="../uploads/<?php echo htmlspecialchars($recipe['video']); ?>" type="video/webm">
                Your browser does not support the video tag.
            </video>
        <?php endif; ?>

        <!-- Recipe Description -->
        <p class="mt-4"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>

        <!-- Ingredients -->
        <h2 class="text-xl font-bold mt-6">Ingredients</h2>
        <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></p>

        <!-- Steps -->
        <h2 class="text-xl font-bold mt-6">Steps</h2>
        <p class="whitespace-pre-line"><?php echo nl2br(htmlspecialchars($recipe['steps'])); ?></p>

        <!-- Save Recipe Button -->
        <?php if ($user_id): ?>
            <form action="save_recipe.php" method="POST" class="mt-6">
                <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
                <button type="submit" class="bg-<?php echo $saved ? 'red' : 'green'; ?>-500 text-white px-4 py-2 rounded">
                    <?php echo $saved ? "Unsave Recipe" : "Save Recipe"; ?>
                </button>
            </form>
        <?php endif; ?>

        <!-- Rating Form -->
        <?php if ($user_id): ?>
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
        <?php if ($user_id): ?>
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