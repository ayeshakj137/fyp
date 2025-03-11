<?php
session_start();
session_destroy();
header("Location: ../index.php"); // Adjust if needed based on your folder structure
exit;
?>
