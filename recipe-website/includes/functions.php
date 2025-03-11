<?php
// functions.php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>