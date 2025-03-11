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

// Insert recipe categories (Run only once, will ignore duplicates)
$categoryQuery = "INSERT IGNORE INTO recipe_categories (category_name) VALUES 
('Appetizer'), ('Beverages'), ('Vegan'), ('Vegetarian'), ('Gluten-Free'), 
('Seafood'), ('Salad'), ('Soup'), ('Healthy')";
mysqli_query($conn, $categoryQuery);

// Fetch categories for the dropdown
$categories = mysqli_query($conn, "SELECT * FROM recipe_categories");

// Default values for Chicken Karahi
$defaultTitle = "Chicken Karahi";
$defaultDescription = "A delicious Pakistani/Indian chicken curry cooked with tomatoes, spices, and herbs.";
$defaultIngredients = "500g chicken (bone-in, cut into small pieces)\n3 medium tomatoes (chopped or blended)\n2 medium onions (finely sliced)\n4 cloves garlic (chopped)\n1-inch ginger (julienned)\n2 green chilies (sliced)\n½ cup yogurt\n½ cup oil or ghee\n1 teaspoon cumin seeds\n1 teaspoon coriander powder\n1 teaspoon red chili powder\n½ teaspoon turmeric powder\n1 teaspoon salt (adjust to taste)\n1 teaspoon garam masala\n1 teaspoon black pepper\n1 tablespoon butter (optional, for richness)\nFresh coriander (for garnish)";
$defaultSteps = "1. Heat oil in a pan and add cumin seeds.\n2. Add sliced onions and sauté until golden brown.\n3. Add chopped garlic and ginger, cook for 30 seconds.\n4. Add chicken pieces and cook until browned.\n5. Add chopped/blended tomatoes, yogurt, and all spices.\n6. Cook on medium heat until the oil separates.\n7. Add green chilies and butter, then simmer for 5 minutes.\n8. Garnish with fresh coriander and serve hot.";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);
    $steps = mysqli_real_escape_string($conn, $_POST['steps']);
    $category_id = (int) $_POST['category_id'];
    $prep_time = (int) $_POST['prep_time'];
    $cook_time = (int) $_POST['cook_time'];
    $servings = (int) $_POST['servings'];
    $user_id = $_SESSION['user_id'];

    // Handle image upload securely
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = __DIR__ . '/../uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $imageName;
        }
    }

    // Handle video upload securely
    $video = '';
    if (!empty($_FILES['video']['name'])) {
        $target_dir = __DIR__ . '/../uploads/';
        $videoName = time() . '_' . basename($_FILES['video']['name']);
        $target_file = $target_dir . $videoName;

        if (move_uploaded_file($_FILES['video']['tmp_name'], $target_file)) {
            $video = $videoName;
        }
    }

    // Insert the recipe into the database
    $stmt = mysqli_prepare($conn, "INSERT INTO recipes 
        (user_id, title, description, ingredients, steps, category_id, image, video, prep_time, cook_time, servings) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "issssisssii", $user_id, $title, $description, $ingredients, $steps, $category_id, $image, $video, $prep_time, $cook_time, $servings);

    if (mysqli_stmt_execute($stmt)) {
        redirect(URL_ROOT . '/pages/recipe.php?id=' . mysqli_insert_id($conn));
    } else {
        $error = "Error adding recipe. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Recipe - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold">Create Recipe</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <form action="create_recipe.php" method="post" enctype="multipart/form-data" class="mt-4">
            <label for="title" class="block">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($defaultTitle); ?>" required class="p-2 border border-gray-300 rounded w-full">

            <label for="description" class="block mt-4">Description:</label>
            <textarea name="description" required class="p-2 border border-gray-300 rounded w-full"><?php echo htmlspecialchars($defaultDescription); ?></textarea>

            <label for="ingredients" class="block mt-4">Ingredients:</label>
            <textarea name="ingredients" required class="p-2 border border-gray-300 rounded w-full"><?php echo htmlspecialchars($defaultIngredients); ?></textarea>

            <label for="steps" class="block mt-4">Steps:</label>
            <textarea name="steps" required class="p-2 border border-gray-300 rounded w-full"><?php echo htmlspecialchars($defaultSteps); ?></textarea>

            <label for="category_id" class="block mt-4">Category:</label>
            <select name="category_id" required class="p-2 border border-gray-300 rounded w-full">
                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $category['category_id']; ?>">
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="prep_time" class="block mt-4">Prep Time (minutes):</label>
            <input type="number" name="prep_time" required class="p-2 border border-gray-300 rounded w-full">

            <label for="cook_time" class="block mt-4">Cook Time (minutes):</label>
            <input type="number" name="cook_time" required class="p-2 border border-gray-300 rounded w-full">

            <label for="servings" class="block mt-4">Servings:</label>
            <input type="number" name="servings" required class="p-2 border border-gray-300 rounded w-full">

            <label for="image" class="block mt-4">Recipe Image:</label>
            <input type="file" name="image" class="p-2 border border-gray-300 rounded w-full">

            <label for="video" class="block mt-4">Recipe Video:</label>
            <input type="file" name="video" accept="video/*" class="p-2 border border-gray-300 rounded w-full">

            <button type="submit" class="bg-blue-500 text-white p-2 rounded mt-4">Create Recipe</button>
        </form>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
