<?php
// login.php - Process login from index.php
session_start();
require_once 'accounts.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check credentials
    if (isset($accounts[$username]) && $accounts[$username] === $password) {
        // Successful login
        $_SESSION['user'] = $username;
        header('Location: managerview.php');
        exit;
    } else {
        // Invalid credentials
        header('Location: index.php?error=invalid');
        exit;
    }
} else {
    // Redirect to login form if accessed directly
    header('Location: index.php');
    exit;
}
