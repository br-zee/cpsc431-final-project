<?php
// register.php - Basic Registration Page for CSUF Volleyball Team
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Ensure password fields match (case-sensitive)
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match. Please try again.';
    } else {
        // TODO: Add database insertion logic here
        $success = 'Registration successful! You can now log in.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CSUF Volleyball Team</title>
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
        .register-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
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
        .btn-create {
            background-color: #28A745;
            color: #fff;
        }
        .btn-back {
            background-color: #6c757d;
            color: #fff;
        }
        .message {
            margin-bottom: 10px;
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if ($error): ?>
            <div class="message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <input type="text" name="username" placeholder="Enter new username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
            <input type="password" name="password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit" class="btn-create">Create Account</button>
            <button type="button" class="btn-back" onclick="window.location.href='index.php'">Back</button>
        </form>
    </div>
</body>
</html>
