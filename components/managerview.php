<?php
// managerview.php - Protected manager view
// session_start();

// Simple check to prevent direct access without login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='global.css' />
    <title>Manager View - CSUF Volleyball Team</title>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']) ?></h2>
        <form action="pages/logout.php" method="post">
            <button type="submit">Log Out</button>
        </form>
    </div>
</body>
</html>

<style>
    .container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .container button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
        background-color: #dc3545;
        color: #fff;
    }
</style>
