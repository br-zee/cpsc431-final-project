<?php 
if (!isset($_SESSION['user']) || (isset($_SESSION['role']) && $_SESSION['role'] < 1)) {
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
    <h1 class="header">Edit Personal Info</h1>
    <?php 

    $db = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);
    if ($db->connect_error) {
        die("Connection failed: " . htmlspecialchars($db->connect_error));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $userID = $_POST['userID'];

        // Updating Player's address information
        if (isset($_POST['street'])) {
            $street = $_POST['street'];
            $building = $_POST['building'];
            $city = $_POST['city'];
            $country = $_POST['country'];
            $zipCode = $_POST['zip'];

            $query = '
            UPDATE AddressInfo
            SET street = ?, building = ?, city = ?, country = ?, zipCode = ?
            WHERE userID = ?
            ';

            $stmt = $db->prepare($query);
            $stmt->bind_param("ssssss", $street, $building, $city, $country, $zipCode, $userID);
        }

        // Deleting Player's address information
        else {
            $query = '
            DELETE FROM AddressInfo
            WHERE userID = ?
            ';
            $stmt = $db->prepare($query);
            $stmt->bind_param('s', $userID);
        }
       
       $stmt->execute();
       $stmt->free_result();
    }
    
    // Get user role priority
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

    if ($priority >= 3) {
        $query = '
        SELECT 
        P.playerID, P.firstName, P.lastName, P.positionID, P.userID,
        AI.street, AI.building, AI.city, AI.country, AI.zipCode
        FROM Player as P
        LEFT JOIN AddressInfo as AI ON P.userID = AI.userID;
        ';

        $stmt = $db->prepare($query);   
    }
    else if ($priority >= 1) {
        $query = '
        SELECT 
        P.playerID, P.firstName, P.lastName, P.positionID, P.userID,
        AI.street, AI.building, AI.city, AI.country, AI.zipCode
        FROM Player as P
        LEFT JOIN AddressInfo as AI ON P.userID = AI.userID
        WHERE P.userID = ?
        '; 
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $_SESSION['user']);
    }
    else {
        exit;
    }

    $stmt->bind_result($playerID, $firstName, $lastName, $positionID, $userID, $street, $building, $city, $country, $zipCode);
    $stmt->execute();

    $playerArr = [];
    while ($stmt->fetch()) {
        $playerArr[$playerID] = (object)[
            'firstName' => $firstName,
            'lastName' => $lastName,
            'positionID' => $positionID,
            'userID' => $userID,
            'street' => $street,
            'building' => $building,
            'city' => $city,
            'country' => $country,
            'zipCode' => $zipCode
        ];
    }
    ?>

    <form action="" method="GET">
        <input type="hidden" name="page" value="1" />
        <label for="playerID">Select Player:</label>
        <select name="playerID" id="playerID" onchange="this.form.submit()">
            <option value="">-- choose player --</option>
            <?php 
            foreach($playerArr as $id => $p) {
                ?>
                <option value=<?= $id ?> <?= isset($_GET['playerID']) && $_GET['playerID'] == $id ? 'selected' : ''?>>
                    <?=  $p->firstName.' '.$p->lastName ?>
                </option>
                <?php 
            }
            ?>
        </select>
    </form>

    <?php 
    
    if (isset($_GET['playerID']) && $_GET['playerID'] != '' && $playerArr[$_GET['playerID']]->userID) {
        ?>
        <div class="address-box">

            <div class="current-address">
                <h1>Current Address</h1>
                <?php 
                    $curStreet = $playerArr[$_GET['playerID']]->street;
                    $curBuilding = isset($playerArr[$_GET['playerID']]->building) 
                        ? ', '.$playerArr[$_GET['playerID']]->building 
                        : '';
                    $curCity = $playerArr[$_GET['playerID']]->city;
                    $curCountry = $playerArr[$_GET['playerID']]->country;
                    $curZip = $playerArr[$_GET['playerID']]->zipCode;
                    $curPosition = $_SESSION['role'] == 'Player' ? $playerArr[$_GET['playerID']]->positionID : ''; 

                    $fullAddress = $curStreet.$curBuilding.' '.$curCity.', '.$curCountry.' '.$curZip;
                ?>
                <p><?=$fullAddress?></p>
            </div>

            <form action="" method="POST">
                <h1>Update Address Info for <?= $playerArr[$_GET['playerID']]->firstName.' '.$playerArr[$_GET['playerID']]->lastName ?></h1>
                <label for="street">Street</label>
                <input type="text" name="street" id="street">
                <label for="building">Building (optional)</label>
                <input type="text" name="building" id="building">
                <label for="city">City</label>
                <input type="text" name="city" id="city">
                <label for="country">Country</label>
                <input type="text" name="country" id="country">
                <label for="zip">Zip Code</label>
                <input type="number" name="zip" id="zip">
                <input type="hidden" name="userID" value=<?= $playerArr[$_GET['playerID']]->userID ?>>
                <input type="submit">
            </form>
        </div>

        <form action="" method="POST">
            <h1>Delete Address Info for <?= $playerArr[$_GET['playerID']]->firstName.' '.$playerArr[$_GET['playerID']]->lastName ?></h1>

            <input type="hidden" name="userID" value=<?= $playerArr[$_GET['playerID']]->userID ?>>
            <input type="submit" value="Delete">
        </form>
        <?php 
    }    
    else {
        ?>
        <p style="text-align:center; color:red;">Player does not have an account!</p>
        <p style="text-align:center;">Contact manager@gmail.com if you have an account to link</p>
        <?php 
    }
    $stmt->free_result();
    $db->close();
    ?>
</body>
</html>

<style>
    .header {
        text-align: center;
    }
    form {
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }
    form label { 
        margin-bottom: 8px; font-weight: bold;   
    }
    form select, input[type="number"], input[type="text"] {
      padding: 8px; 
      width: 200px; 
      margin-bottom: 12px; 
      border: 1px solid #ccc; 
      border-radius: 4px;
    }

    .address-box {
        display: flex;
        justify-content: center;
        gap: 50px;
    }
    .current-address {
        display: flex;
        flex-direction: column;
        /* justify-content: center; */
    }
    .current-address p{
        margin: 10px 0;
    }
    .current-address h1 {
        margin-bottom: 10px;
    }
</style>