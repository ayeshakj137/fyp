<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Ensure the user is logged in
if (!isLoggedIn()) {
    redirect(URL_ROOT . '/pages/login.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $ingredients = sanitizeInput($_POST['ingredients']);
    $steps = sanitizeInput($_POST['steps']);
    $category_id = sanitizeInput($_POST['category_id']);
    $prep_time = sanitizeInput($_POST['prep_time']);
    $cook_time = sanitizeInput($_POST['cook_time']);
    $servings = sanitizeInput($_POST['servings']);
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = __DIR__ . '/../uploads/';
        $target_file = $target_dir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = basename($_FILES['image']['name']);
        }
    }

    // Insert the recipe into the database
    $stmt = mysqli_prepare($conn, "INSERT INTO recipes (user_id, title, description, ingredients, steps, category_id, image, prep_time, cook_time, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssisiii", $user_id, $title, $description, $ingredients, $steps, $category_id, $image, $prep_time, $cook_time, $servings);
    if (mysqli_stmt_execute($stmt)) {
        redirect(URL_ROOT . '/pages/recipe.php?id=' . mysqli_insert_id($conn));
    } else {
        $error = "Error adding recipe. Please try again.";
    }
}

// Fetch categories for the dropdown
$categories = mysqli_query($conn, "SELECT * FROM recipe_categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold">Add Recipe</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <form action="add_recipe.php" method="post" enctype="multipart/form-data" class="mt-4">
            <label for="title" class="block">Title:</label>
            <input type="text" name="title" required class="p-2 border border-gray-300 rounded">

            <label for="description" class="block mt-4">Description:</label>
            <textarea name="description" required class="p-2 border border-gray-300 rounded"></textarea>

            <label for="ingredients" class="block mt-4">Ingredients:</label>
            <textarea name="ingredients" required class="p-2 border border-gray-300 rounded"></textarea>

            <label for="steps" class="block mt-4">Steps:</label>
            <textarea name="steps" required class="p-2 border border-gray-300 rounded"></textarea>

            <label for="category_id" class="block mt-4">Category:</label>
            <select name="category_id" required class="p-2 border border-gray-300 rounded">
                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="prep_time" class="block mt-4">Prep Time (minutes):</label>
            <input type="number" name="prep_time" required class="p-2 border border-gray-300 rounded">

            <label for="cook_time" class="block mt-4">Cook Time (minutes):</label>
            <input type="number" name="cook_time" required class="p-2 border border-gray-300 rounded">

            <label for="servings" class="block mt-4">Servings:</label>
            <input type="number" name="servings" required class="p-2 border border-gray-300 rounded">

            <label for="image" class="block mt-4">Recipe Image:</label>
            <input type="file" name="image" class="p-2 border border-gray-300 rounded">

            <button type="submit" class="bg-blue-500 text-white p-2 rounded mt-4">Add Recipe</button>
        </form>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>