<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include configuration and functions
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/functions.php';

// Ensure the user is an admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect(URL_ROOT);
}

// Ensure the report ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(URL_ROOT . '/admin.php');
}

$report_id = sanitizeInput($_GET['id']);

// Resolve the report
$stmt = $conn->prepare("UPDATE reports SET status = 'resolved' WHERE report_id = ?");
$stmt->bind_param("i", $report_id);
if ($stmt->execute()) {
    redirect(URL_ROOT . '/admin.php');
} else {
    die("Error resolving report.");
}
?>