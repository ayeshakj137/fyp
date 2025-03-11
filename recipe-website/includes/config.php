<?php
// config.php

// Start the session if it hasn't already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$host = 'localhost';
$dbname = 'recipe_website';
$username = 'root';
$password = '';

// Create a mysqli connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Define constants
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost/recipe-website');
define('SITE_NAME', 'Recipe Website');
?>