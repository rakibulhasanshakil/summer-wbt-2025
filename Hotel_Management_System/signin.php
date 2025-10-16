<?php
require_once "includes/db.php";
session_start();

// Redirect if already logged in
if (isset($_SESSION['usertype'])) {
    $u = $_SESSION['usertype'];
    header("Location: /hotel_management_system/dashboards/dashboard_{$u}.php");
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Array to store validation errors
    $validation_errors = [];
    
    // Validate email
    if (empty($email)) {
        $validation_errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validation_errors[] = "Please enter a valid email address";
    }
    
    // Validate password
    if (empty($password)) {
        $validation_errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $validation_errors[] = "Password must be at least 6 characters long";
    }
    
    if (empty($validation_errors)) {
        $stmt = $conn->prepare("SELECT id, fullname, email, phone, password, usertype, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if ($user['status'] !== 'active') {
                $error = $user['status'] === 'pending' ? 
                    "Account pending approval. Contact admin." : 
                    "Account is blocked. Contact admin.";
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['usertype'] = $user['usertype'];
                header("Location: /hotel_management_system/dashboards/dashboard_{$user['usertype']}.php");
                exit;
            } else {
                $error = "Invalid email/password.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
}
?>

<?php include("includes/header.php"); ?>
<link rel="stylesheet" href="/Hotel_Management_System/css/auth.css">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="brand-logo">
                    <i class="fas fa-hotel"></i>
                </div>
                <h2>Welcome Back to</h2>
                <h1>Grand Palace Hotel</h1>
                <div class="hotel-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Experience luxury at its finest. Sign in to access your exclusive member benefits.</p>
                <div class="auth-features">
                    <div class="feature">
                        <i class="fas fa-concierge-bell"></i>
                        <span>24/7 Concierge</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-percent"></i>
                        <span>Member Discounts</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-gift"></i>
                        <span>Loyalty Rewards</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <h2>Sign In</h2>
                    <p>Please enter your credentials to continue</p>
                </div>

                <?php if (!empty($validation_errors)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <?php foreach($validation_errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error) && !empty($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="" class="auth-form">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="Enter your email address" 
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="password-field">
                            <input 
                                type="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                            >
                            <button type="button" class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        
                        </label>
                        <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </button>
                    </div>

                    <div class="form-links">
                        <p>Don't have an account? <a href="signup.php">Create Account</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = document.querySelector('.password-toggle');
    const passwordInput = document.querySelector('input[name="password"]');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Form animation
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(group => {
        const input = group.querySelector('input');
        input.addEventListener('focus', () => group.classList.add('focused'));
        input.addEventListener('blur', () => {
            if (!input.value) {
                group.classList.remove('focused');
            }
        });
        if (input.value) {
            group.classList.add('focused');
        }
    });
});
</script>

<?php include("includes/footer.php"); ?>
