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

// Fetch the user ID from the query parameter or session
$profile_user_id = isset($_GET['id']) ? sanitizeInput($_GET['id']) : $_SESSION['user_id'];
$user_id = $_SESSION['user_id'];

// Fetch the profile user's details
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $profile_user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$profile_user = mysqli_fetch_assoc($result);

if (!$profile_user) {
    die("User not found.");
}

// Fetch the profile user's uploaded recipes
$recipes_query = "SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC";
$recipes_stmt = mysqli_prepare($conn, $recipes_query);
mysqli_stmt_bind_param($recipes_stmt, "i", $profile_user_id);
mysqli_stmt_execute($recipes_stmt);
$recipes_result = mysqli_stmt_get_result($recipes_stmt);

// Fetch the profile user's saved recipes
$saved_query = "SELECT r.* FROM saved_recipes s 
                JOIN recipes r ON s.recipe_id = r.recipe_id 
                WHERE s.user_id = ? ORDER BY s.saved_at DESC";
$saved_stmt = mysqli_prepare($conn, $saved_query);
mysqli_stmt_bind_param($saved_stmt, "i", $profile_user_id);
mysqli_stmt_execute($saved_stmt);
$saved_result = mysqli_stmt_get_result($saved_stmt);

// Fetch the profiles of users the profile user is following
$followed_query = "SELECT u.* FROM followers f 
                   JOIN users u ON f.following_id = u.user_id 
                   WHERE f.follower_id = ?";
$followed_stmt = mysqli_prepare($conn, $followed_query);
mysqli_stmt_bind_param($followed_stmt, "i", $profile_user_id);
mysqli_stmt_execute($followed_stmt);
$followed_result = mysqli_stmt_get_result($followed_stmt);

// Handle form submission for profile update (if viewing own profile)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_user_id == $user_id) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);

    // Handle profile picture upload
    $profile_pic = $profile_user['profile_pic'];
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = __DIR__ . '/../uploads/';
        $target_file = $target_dir . basename($_FILES['profile_pic']['name']);
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            $profile_pic = basename($_FILES['profile_pic']['name']);
        }
    }

    // Update the user's profile
    $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ?, profile_pic = ? WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $profile_pic, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        echo "<p class='bg-green-500 text-white p-2 rounded'>Profile updated successfully!</p>";
    } else {
        $error = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($profile_user['name']); ?>'s Profile</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <!-- Profile Information -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg">
            <h2 class="text-xl font-bold">Profile Information</h2>
            <?php if ($profile_user_id == $user_id): ?>
                <form action="profile.php" method="post" enctype="multipart/form-data" class="mt-4">
                    <label for="name" class="block">Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($profile_user['name']); ?>" required class="p-2 border border-gray-300 rounded w-full">

                    <label for="email" class="block mt-4">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($profile_user['email']); ?>" required class="p-2 border border-gray-300 rounded w-full">

                    <label for="profile_pic" class="block mt-4">Profile Picture:</label>
                    <input type="file" name="profile_pic" class="p-2 border border-gray-300 rounded w-full">

                    <?php if ($profile_user['profile_pic']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($profile_user['profile_pic']); ?>" alt="Profile Picture" class="w-32 h-32 object-cover rounded mt-4">
                    <?php endif; ?>

                    <button type="submit" class="bg-blue-500 text-white p-2 rounded mt-4">Update Profile</button>
                </form>
            <?php else: ?>
                <p class="mt-4">Email: <?php echo htmlspecialchars($profile_user['email']); ?></p>
                <?php if ($profile_user['profile_pic']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($profile_user['profile_pic']); ?>" alt="Profile Picture" class="w-32 h-32 object-cover rounded mt-4">
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Followed Profiles -->
        <div class="mt-8">
            <h2 class="text-xl font-bold">Followed Profiles</h2>
            <?php if (mysqli_num_rows($followed_result) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <?php while ($followed_user = mysqli_fetch_assoc($followed_result)): ?>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow-lg">
                            <?php if ($followed_user['profile_pic']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($followed_user['profile_pic']); ?>" 
                                     alt="<?php echo htmlspecialchars($followed_user['name']); ?>" 
                                     class="w-32 h-32 object-cover rounded mx-auto">
                            <?php endif; ?>
                            <h3 class="text-lg font-bold mt-2 text-center"><?php echo htmlspecialchars($followed_user['name']); ?></h3>
                            <p class="text-sm text-center"><?php echo htmlspecialchars($followed_user['email']); ?></p>
                            <a href="profile.php?id=<?php echo $followed_user['user_id']; ?>" class="text-blue-500 mt-4 block text-center">View Profile</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 mt-4">This user is not following anyone yet.</p>
            <?php endif; ?>
        </div>

        <!-- User's Uploaded Recipes -->
        <div class="mt-8">
            <h2 class="text-xl font-bold">Uploaded Recipes</h2>
            <?php if (mysqli_num_rows($recipes_result) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <?php while ($recipe = mysqli_fetch_assoc($recipes_result)): ?>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow-lg">
                            <img src="../uploads/<?php echo htmlspecialchars($recipe['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($recipe['title']); ?>" 
                                 class="w-full h-48 object-cover rounded">
                            <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p class="text-sm"><?php echo htmlspecialchars($recipe['description']); ?></p>
                            <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="text-blue-500 mt-4 inline-block">View Recipe</a>

                            <!-- Edit and Delete Buttons (only show if the logged-in user owns the recipe) -->
                            <?php if ($profile_user_id == $user_id): ?>
                                <div class="mt-4">
                                    <a href="edit_recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="bg-yellow-500 text-white p-2 rounded">Edit</a>
                                    <form action="delete_recipe.php" method="POST" class="inline-block ml-2">
                                        <input type="hidden" name="recipe_id" value="<?php echo $recipe['recipe_id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white p-2 rounded" onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 mt-4">This user hasn't uploaded any recipes yet.</p>
            <?php endif; ?>
        </div>

        <!-- User's Saved Recipes -->
        <div class="mt-8">
            <h2 class="text-xl font-bold">Saved Recipes</h2>
            <?php if (mysqli_num_rows($saved_result) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <?php while ($recipe = mysqli_fetch_assoc($saved_result)): ?>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow-lg">
                            <img src="../uploads/<?php echo htmlspecialchars($recipe['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($recipe['title']); ?>" 
                                 class="w-full h-48 object-cover rounded">
                            <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p class="text-sm"><?php echo htmlspecialchars($recipe['description']); ?></p>
                            <a href="recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="text-blue-500 mt-4 inline-block">View Recipe</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 mt-4">This user hasn't saved any recipes yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>