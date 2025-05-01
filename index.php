<?php
// index.php - CSUF Volleyball Team Login Page
include_once("components/navbar.php");
include_once("protected/adaptation.php");
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

        $stmt->free_result();
        $db->close();
    ?>

    <div class="page-buttons">
        <?php 
        if ($priority >= 0) {
            /* render only game statistics*/ 
            ?>
            <form action="" method="GET">
                <input type="hidden" name="page" value=0>
                <button type="submit">View Game Data</button>
            </form>
            <?php                    
        }
        if ($priority >= 1) {
            /* render Player abilities: page for viewing/changing personal info */ 
            ?>
            <form action="" method="GET">
                <input type="hidden" name="page" value=1>
                <button type="submit">Edit Personal Info</button>
            </form>
            <?php
        }
        if ($priority >= 2) {
            /* render Coach abilities and below: page for updating/deleting statistics */ 
            ?>
            <form action="" method="GET">
                <input type="hidden" name="page" value=2>
                <button type="submit">Edit Statistics</button>
            </form>
            <?php
        }
        if ($priority >= 3) { 
            /* render Manager abilities and below: page for updating/deleting all, linling players to roles */ 
            ?>
            <form action="" method="GET">
                <input type="hidden" name="page" value=3>
                <button type="submit">Add User to Role</button>
            </form>
            <?php
        }   
        ?>
    </div>

    <div class="content">
        <?php 
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 0) {
                include_once('components/viewGameData.php');
            }
            if ($_GET['page'] == 1 && $priority >= 1) {
                include_once('components/editPlayerInfo.php');
            }
            if ($_GET['page'] == 2 && $priority >= 2) {
                include_once('components/editStatistics.php');
            }
            if ($_GET['page'] == 3 && $priority >= 3) {
                include_once('components/editAccountRoles.php');
            }
        }
        else include_once('components/viewGameData.php');
        ?>
    </div>

</body>
</html>

<style>
    .page-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-block: 20px;
    }

    .page-buttons button {
        border: none;
        background: white;
        border-radius: 50px;
        padding: 10px 20px;
        cursor: pointer;
    }
    .page-buttons button:hover {
        background: lightgray;
    }
</style>
