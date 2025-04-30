<?php
// login.php - Process login from index.php

include_once("../protected/adaptation.php");

session_start();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);
    if (mysqli_connect_errno()) {
        echo "Could not connect to database. Please try again later.";
        exit;
    }

    $query = "
        SELECT *
        FROM Account
        WHERE userID = ? AND userPassword = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();

    $stmt->store_result();
    $stmt->bind_result($userID, $userPassword, $userEmail, $roleID);    

    $stmt->fetch();

    $stmt->close();
    $db->close();

    return;

    // Check credentials
    if (isset($accounts[$username]) && $accounts[$username] === $password) {
        // Successful login
        $_SESSION['user'] = $username;
        header('Location: managerview.php');
        exit;
    } else {
        // Invalid credentials
        header('Location: ../index.php?error=invalid');
        exit;
    }
} else {
    // Redirect to login form if accessed directly
    header('Location: index.php');
    exit;
}
