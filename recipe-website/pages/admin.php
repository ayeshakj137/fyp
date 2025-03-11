<?php
// ... (existing code)

// Fetch all reports
$reports = mysqli_query($conn, "SELECT reports.*, users.name AS reporter, recipes.title AS recipe_title 
    FROM reports 
    JOIN users ON reports.user_id = users.user_id 
    JOIN recipes ON reports.recipe_id = recipes.recipe_id 
    ORDER BY reports.created_at DESC");

// Fetch all recipes for deletion
$recipes = mysqli_query($conn, "SELECT recipes.*, users.name AS author 
    FROM recipes 
    JOIN users ON recipes.user_id = users.user_id 
    ORDER BY recipes.created_at DESC");

// Fetch all admin logs
$admin_logs = mysqli_query($conn, "SELECT admin_logs.*, users.name AS admin_name 
    FROM admin_logs 
    JOIN users ON admin_logs.admin_id = users.user_id 
    ORDER BY admin_logs.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (existing head content) -->
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-white">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="p-6 mt-16">
        <h1 class="text-2xl font-bold">Admin Panel</h1>

        <!-- Reports Table -->
        <h2 class="text-xl font-bold mt-6">Reports</h2>
        <table class="w-full mt-4">
            <thead>
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Recipe</th>
                    <th class="border p-2">Reporter</th>
                    <th class="border p-2">Reason</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Created At</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($report = mysqli_fetch_assoc($reports)): ?>
                    <tr>
                        <td class="border p-2"><?php echo $report['report_id']; ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($report['recipe_title']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($report['reporter']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($report['reason']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($report['status']); ?></td>
                        <td class="border p-2"><?php echo $report['created_at']; ?></td>
                        <td class="border p-2">
                            <a href="resolve_report.php?id=<?php echo $report['report_id']; ?>" class="text-blue-500">Resolve</a>
                            <a href="delete_recipe.php?id=<?php echo $report['recipe_id']; ?>" class="text-red-500 ml-2">Delete Recipe</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Recipes Table -->
        <h2 class="text-xl font-bold mt-6">Recipes</h2>
        <table class="w-full mt-4">
            <thead>
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Title</th>
                    <th class="border p-2">Author</th>
                    <th class="border p-2">Created At</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($recipe = mysqli_fetch_assoc($recipes)): ?>
                    <tr>
                        <td class="border p-2"><?php echo $recipe['recipe_id']; ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($recipe['title']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($recipe['author']); ?></td>
                        <td class="border p-2"><?php echo $recipe['created_at']; ?></td>
                        <td class="border p-2">
                            <a href="delete_recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="text-red-500">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Admin Logs Table -->
        <h2 class="text-xl font-bold mt-6">Admin Logs</h2>
        <table class="w-full mt-4">
            <thead>
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Admin</th>
                    <th class="border p-2">Action</th>
                    <th class="border p-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = mysqli_fetch_assoc($admin_logs)): ?>
                    <tr>
                        <td class="border p-2"><?php echo $log['log_id']; ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($log['admin_name']); ?></td>
                        <td class="border p-2"><?php echo htmlspecialchars($log['action']); ?></td>
                        <td class="border p-2"><?php echo $log['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>