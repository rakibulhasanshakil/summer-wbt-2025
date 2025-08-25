<?php
session_start();
include("includes/database.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Handle profile update
if(isset($_POST['update'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $conn->query("UPDATE users SET username='$username', email='$email' WHERE id=$user_id");
    echo "Profile updated successfully!";
    $user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
}

// Handle password change
if(isset($_POST['change_pass'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];

    // Check current password
    if(password_verify($current, $user['password'])) {
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$new_hash' WHERE id=$user_id");
        echo "Password changed successfully!";
    } else {
        echo "Current password is incorrect!";
    }
}

// Handle account deletion
if(isset($_POST['delete_account'])) {
    $conn->query("DELETE FROM users WHERE id=$user_id");
    session_destroy();
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
</head>
<body>
    <h2>My Profile</h2>

    <h3>Update Profile</h3>
    <form method="POST">
        Username: <input type="text" name="username" value="<?= $user['username'] ?>" required><br>
        Email: <input type="email" name="email" value="<?= $user['email'] ?>" required><br>
        <button type="submit" name="update">Update Profile</button>
    </form>

    <h3>Change Password</h3>
    <form method="POST">
        Current Password: <input type="password" name="current_password" required><br>
        New Password: <input type="password" name="new_password" required><br>
        <button type="submit" name="change_pass">Change Password</button>
    </form>

    <h3>Delete Account</h3>
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
        <button type="submit" name="delete_account">Delete My Account</button>
    </form>
</body>
</html>
