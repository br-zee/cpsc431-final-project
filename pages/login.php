<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../global.css" />
</head>
<body>

<?php
// login.php - Process login from index.php

include_once("../protected/adaptation.php");
include_once("../components/navbar.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);
    if (mysqli_connect_errno()) {
        echo "Could not connect to database. Please try again later.";
        exit;
    }

    $query = "
        SELECT userID, userEmail, roleID
        FROM Account
        WHERE userID = ? AND userPassword = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();

    $stmt->store_result();
    $stmt->bind_result($userID, $userEmail, $roleID);    

    $stmt->fetch();

    $stmt->close();
    $db->close();

    // Check credentials
    if ($userID) {
        // Successful login
        $_SESSION['user'] = $username;
        $_SESSION['email'] = $userEmail;
        $_SESSION['role'] = $roleID;

        header('Location: ../index.php');
        exit;
    } else {
        // Invalid credentials
        header('Location: ?error=invalid');
        exit;
    }
}
?>

    <div class="login-container">
        <h1 class="title"><span class="csuf">CSUF</span> <span class="team">Volleyball Team</span></h1>
        <form method="post" action="">
            
            <?php if ($_GET['error'] == 'invalid') { ?>
            <p style="color: red">Invalid username/password</p>   
            <?php } ?>

            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-login">Log In</button>
        </form>
        <button type="button" class="btn-register" onclick="window.location.href='register.php'">Register</button>
    </div>
    
</body>
</html>

<style>
    .login-container {
        width: 30%;
        max-width: 300px;
        margin: 50px auto;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .title .csuf {
        color: blue;
    }
    .title .team {
        color: orange;
    }
    .title {
        margin-bottom: 20px;
        font-size: 2em;
    }
    input {
        display: block;
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }
    button {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
    }
    .btn-login {
        background-color: #007BFF;
        color: #fff;
    }
    .btn-register {
        background-color: #6c757d;
        color: #fff;
    }
</style>
