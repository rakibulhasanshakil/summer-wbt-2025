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
<link rel="stylesheet" href="/hotel_management_system/css/auth.css">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h2>Grand Palace Hotel</h2>
                </div>
                <div class="title-container">
                    <div class="title-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h1>Password Recovery</h1>
                    <p class="auth-description">
                        <i class="fas fa-info-circle"></i>
                        Enter your registered email address to receive password reset instructions
                    </p>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="message error">
                    <div class="message-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="message-content">
                        <strong>Error</strong>
                        <p><?php echo $error; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success">
                    <div class="message-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="message-content">
                        <strong>Success</strong>
                        <p><?php echo $success; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form method="post" class="auth-form">
                <div class="form-field">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <div class="input-group">
                       
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your registered email"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <div class="btn-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <span>Send Reset Instructions</span>
                </button>

                <div class="auth-links">
                    <a href="signin.php" class="link-primary">
                        <div class="link-icon">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                        <span>Return to Sign In</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
<?php include("includes/footer.php"); ?>
