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
    <?php 
        require_once __DIR__ . '/../protected/adaptation.php';

        $db = new mysqli(DB_HOST, USER_NAME, USER_PASS, DB_NAME);

        // Fetch all accounts with a priority less than manager
        $getAccounts = '
            SELECT a.userID, a.teamID, a.userEmail, a.rolePriority, a.firstName, a.lastName, ht.teamName
            FROM Account a, HomeTeam ht
            WHERE a.teamID = ht.teamID AND a.rolePriority < 3
            ORDER BY a.rolePriority DESC
        ';

        $stmt = $db->prepare($getAccounts);
        $stmt->execute();
        $accounts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            foreach ($accounts as $acc) { 

                $newRolePriority = (int)$_POST['roles'][$acc['userID']];
                $newEmail =             $_POST['emails'][$acc['userID']];
                $newFName =             $_POST['fname'][$acc['userID']];
                $newLName =             $_POST['lname'][$acc['userID']];

                if (
                    (int)$acc['rolePriority']  !== $newRolePriority ||
                    $acc['userEmail']          !== $newEmail ||
                    $acc['firstName']          !== $newFName ||
                    $acc['lastName']           !== $newLName 
                ) {
                    $updateQuery = '
                        UPDATE Account
                        SET rolePriority = ?, userEmail = ?, firstName = ?, lastName = ?
                        WHERE userID = ?
                    ';

                    $stmt = $db->prepare($updateQuery);
                    $stmt->bind_param('issss', $newRolePriority, $newEmail, $newFName, $newLName, $acc['userID']);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            header("Location: index.php?page=3&updated=1");
            exit;
        }
        
        $db->close();
    ?>

    <h1 style="text-align:center">Edit User Accounts</h1>
    <p style="text-align:center; color:green;"><?= isset($_GET['updated']) ? 'Updated account info successfully!' : '' ?></p>

    <div class="container">
        <form action="" method="POST" class="account-form">
            <table class="user-list">
                <thead>
                    <th>Username</th>
                    <th>Name (First)</th>
                    <th>Name (Last)</th>
                    <th>Email</th>
                    <th>Role</th>
                </thead>
                <tbody>
                <?php
                foreach ($accounts as $acc):
                    ?>
                    <tr>
                        <td style="background:rgb(208, 229, 245);">
                            <?= $acc['userID'] ?>
                        </td>
                        <td>
                            <input 
                                name="fname[<?= $acc['userID'] ?>]" 
                                type="text" 
                                value="<?= $acc['firstName'] ?>" 
                                onchange="handleFormChange(event)" 
                                minlength="1"
                                required
                            />
                        </td>
                        <td>
                            <input 
                                name="lname[<?= $acc['userID'] ?>]" 
                                type="text" 
                                value="<?= $acc['lastName'] ?>" 
                                onchange="handleFormChange(event)" 
                                minlength="1"
                                required
                            />
                        </td>
                        <td>
                            <input 
                                name="emails[<?= $acc['userID'] ?>]" 
                                type="email" 
                                value="<?= $acc['userEmail'] ?>" 
                                onchange="handleFormChange(event)" 
                                minlength="3"
                                required
                            />
                        </td>
                        <td>
                            <select name="roles[<?= $acc['userID'] ?>]" id="role" onchange="handleFormChange(event)">
                                <?php 
                                $roleArr = ['Guest', 'Player', 'Coach'];
                                $priority = 0;
                                foreach ($roleArr as $role):
                                    ?>
                                    <option value=<?= $priority ?>  <?= $acc['rolePriority'] === $priority ? 'selected' : '' ?>>
                                        <?= $roleArr[$priority++] ?>
                                    </option>
                                    <?php 
                                endforeach
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                endforeach;
                ?>
                </tbody>
            </table>
            <p id="unsaved-changes">There are unsaved changes</p>
            <button type="submit">Submit Changes</button>
        </form>
    </div>
</body>
<script>
    function handleFormChange(e) {
        e.target.style.border = '3px solid crimson';

        const unsavedChanges = document.querySelector("#unsaved-changes");
        unsavedChanges.style.display = 'block';
    }
</script>
</html>

<style>
    * {
        box-sizing: border-box;
    }
    #unsaved-changes {
        display: none;
        color: red;
    }
    .container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        margin-inline: 100px;
        text-align: center;
    }
    .user-list, .user-list th, .user-list td {
        border-collapse: collapse;
        border: 1.5px solid lightgray;
        background: white;
    }
    .user-list {
        width: 100%;
    }
    .user-list th {
        padding: 10px 50px 5px;
        background: #E6EFF6;
    }
    .user-list td {
        padding: 5px;
    }
    .user-list select, input {
        width: 100%;
        border: 3px solid white;
        border-radius: 5px;
    }
    .user-list select:hover, input:hover {
        background: lightgray;
    }
    .account-form button {
        padding: 10px 50px;
        margin-block: 20px;
        border-radius: 10px;
        border: none;
        background: white;
        border: 1px solid gray;
    }
    .account-form button:hover {
        background: lightgray;
    }
</style>
