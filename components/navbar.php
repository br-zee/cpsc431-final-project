<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php 
        require_once __DIR__.'/../config.php';
    ?>
    <nav>
        <a href=<?php echo $BASE_URL.'/index.php' ?>>Home</a>

        <?php 
            session_start();
            $linkUrl = $BASE_URL.'/pages/login.php';
            $userOption = 'Log in';

            if ($_SESSION['user']) {
                $linkUrl = $BASE_URL.'/pages/logout.php';      
                $userOption = 'Log out';         
            }
        ?>
        <a href=<?php echo $linkUrl ?>> <?php echo $userOption ?> </a>
    </nav>
</body>
</html>

<style>
    nav {
        width: 100%;
        background: rgb(20,20,20);
        padding-block: 20px;
        color: white;
    }

    nav a {
        text-decoration: none;
        margin-inline: 20px;
        color: white;
    }
</style>