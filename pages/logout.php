<?php
// logout.php - Log the user out and redirect to index.php
session_start();
// Unset all session variables
$_SESSION = [];
// Destroy the session
session_destroy();

// Redirect back to login page
header('Location: ../index.php');
exit;
?>
