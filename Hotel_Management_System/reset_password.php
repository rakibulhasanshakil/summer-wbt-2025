<?php
session_start();
include("includes/database.php");

$message = "";

if(isset($_GET['token'])) {
    $token = $_GET['token'];
    $result = $conn->query("SELECT * FROM users WHERE reset_token='$token'");
    $user = $result->fetch_assoc();

    if(!$user) {
        die("Invalid or expired token!");
    }

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$new_password', reset_token=NULL WHERE id=".$user['id']);
        $message = "Password has been reset successfully! <a href='login.php'>Login Now</a>";
    }
} else {
    die("No token provided!");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>

    <?php if($message != ""): ?>
        <p><?= $message ?></p>
    <?php else: ?>
    <form method="POST">
        Enter new password: <input type="password" name="new_password" required><br>
        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>
</body>
</html>
