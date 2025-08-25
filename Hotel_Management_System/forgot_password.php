<?php
session_start();
include("includes/database.php");

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if($user) {
        // Generate a temporary reset token
        $token = bin2hex(random_bytes(16));
        $conn->query("UPDATE users SET reset_token='$token' WHERE id=".$user['id']);

        // Simulate sending email by showing the reset link
        $message = "Password reset link (simulate email): <br>";
        $message .= "<a href='reset_password.php?token=$token'>Click here to reset password</a>";
    } else {
        $message = "No account found with that email!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>

    <?php if($message != ""): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        Enter your email: <input type="email" name="email" required><br>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
