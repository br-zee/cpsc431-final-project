<?php
// managerview.php - Protected manager view

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
        
    </div>
</body>
</html>

<style>
    .container {
    }
</style>
