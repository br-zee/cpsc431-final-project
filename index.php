<?php
// index.php - CSUF Volleyball Team Login Page
include_once("components/navbar.php");
include_once("protected/adaptation.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css"/>
    <title>CSUF Volleyball Team</title>
</head>
<body>
    <?php 

        $db = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);
        if (mysqli_connect_errno()) {
            echo "Could not connect to database. Please try again later.";
            exit;
        }

        $query = "
            SELECT rolePriority
            FROM UserRole
            WHERE roleID = ?
        ";

        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $_SESSION['role']);
        $stmt->execute();

        $stmt->store_result();
        $stmt->bind_result($priority);    

        $stmt->fetch();

        $stmt->close();
        $db->close();


        if ($priority >= 0) { /* render only game statistics*/ }
        if ($priority >= 1) { /* render Player abilities: page for viewing/changing personal info */ }
        if ($priority >= 2) { /* render Coach abilities and below: page for updating/deleting statistics */ }
        if ($priority >= 3) { /* render Manager abilities and below: page for updating/deleting all, linling players to roles */ }   
    ?>
</body>
</html>

<style>
</style>
