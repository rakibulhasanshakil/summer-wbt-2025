<?php
require_once "includes/db.php";
session_start();
$error = $success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        $error = "Please enter your email.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $up = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE email=?");
            $up->bind_param("sss", $token, $expires, $email);
            $up->execute();
            $reset_link = "reset_password.php?token=$token";
            $success = "Password reset link generated.<br>
                        <a href='$reset_link'>Click here to reset password</a>";
            $up->close();
        } else {
            $error = "Email not found.";
        }
        $stmt->close();
    }
}
?>
<?php include("includes/header.php"); ?>
<?php include("includes/navbar.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/signin.css">
<div class="container">
    <h2>Forgot Password</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <form method="post">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter registered email" required>
        <button type="submit">Get Reset Link</button>
    </form>

    <p style="text-align:center;margin-top:10px;">
        <a href="signin.php" style="color:#1abc9c;">Back to Login</a>
    </p>
</div>
<?php include("includes/footer.php"); ?>
