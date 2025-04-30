<?php 
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../protected/adaptation.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Page</title>
</head>
<body>
    <?php 

    ?>
</body>
</html>