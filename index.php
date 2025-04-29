<?php
// index.php - CSUF Volleyball Team Login Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" src="./home.css"/>
    <title>CSUF Volleyball Team</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .login-container {
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
</head>
<body>
    <?php 
        include_once("./head.php");
    ?>
    <div class="login-container">
        <h1 class="title"><span class="csuf">CSUF</span> <span class="team">Volleyball Team</span></h1>
        <form method="post" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-login">Log In</button>
        </form>
        <button type="button" class="btn-register" onclick="window.location.href='register.php'">Register</button>
    </div>
</body>
</html>
