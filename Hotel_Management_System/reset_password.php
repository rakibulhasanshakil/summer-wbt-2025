<?php
require_once "includes/db.php";
session_start();

$error = $success = '';
$token = $_GET['token'] ?? '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST['token'] ?? '';
    $newpass = $_POST['newpass'] ?? '';
    $conf = $_POST['conf'] ?? '';

    // Password validation: at least 1 uppercase, 1 number, 1 special char, min 8 chars
    $passwordPattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (!$newpass || !$conf) {
        $error = "Please fill both fields.";
    } elseif ($newpass !== $conf) {
        $error = "Passwords do not match.";
    } elseif (!preg_match($passwordPattern, $newpass)) {
        $error = "Password must include at least 1 uppercase letter, 1 number, 1 special character, and be at least 8 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token=?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $u = $res->fetch_assoc();
            if (strtotime($u['reset_expires']) < time()) {
                $error = "Reset link expired.";
            } else {
                $hash = password_hash($newpass, PASSWORD_DEFAULT);
                $up = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
                $up->bind_param("si", $hash, $u['id']);
                $up->execute();
                $success = "✅ Password reset successful! <a href='signin.php'>Login now</a>";
                $up->close();
            }
        } else {
            $error = "Invalid reset link.";
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
                        <i class="fas fa-lock"></i>
                    </div>
                    <h1>Reset Password</h1>
                    <p class="auth-description">
                        <i class="fas fa-shield-alt"></i>
                        Create a strong password for your account
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
                        <p><?= $error ?></p>
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
                        <p><?= $success ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="post" id="resetForm" class="auth-form">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="form-field">
                    <label for="newpass">
                        New Password
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="newpass" 
                            name="newpass" 
                            required 
                            oninput="checkStrength()"
                            placeholder="Enter your new password"
                        >
                        <div class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div id="strength" class="strength-fill"></div>
                        </div>
                        <small id="strength-text" class="strength-text">
                            <i class="fas fa-info-circle"></i>
                            Password strength: weak
                        </small>
                    </div>
                </div>

                <div class="form-field">
                    <label for="conf">
                        Confirm Password
                    </label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="conf" 
                            name="conf" 
                            required
                            placeholder="Confirm your new password"
                        >
                        <div class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <div class="btn-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <span>Update Password</span>
                </button>

                <div class="auth-links">
                    <a href="signin.php" class="link-primary">
                        <div class="link-icon">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                        <span>Back to Sign In</span>
                    </a>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.password-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});

function checkStrength() {
    const pass = document.getElementById('newpass').value;
    const bar = document.getElementById('strength');
    const text = document.getElementById('strength-text');
    
    let score = 0;
    let checks = {
        length: pass.length >= 8,
        uppercase: /[A-Z]/.test(pass),
        number: /[0-9]/.test(pass),
        special: /[@$!%*?&]/.test(pass)
    };

    score = Object.values(checks).filter(Boolean).length;

    let strength = '';
    let color = '';
    let width = '0%';

    switch(score) {
        case 0:
            strength = 'Very Weak';
            color = '#ff4444';
            width = '10%';
            break;
        case 1:
            strength = 'Weak';
            color = '#ffa700';
            width = '25%';
            break;
        case 2:
            strength = 'Medium';
            color = '#ffee00';
            width = '50%';
            break;
        case 3:
            strength = 'Strong';
            color = '#9dff00';
            width = '75%';
            break;
        case 4:
            strength = 'Very Strong';
            color = '#00ff55';
            width = '100%';
            break;
    }

    bar.style.width = width;
    bar.style.backgroundColor = color;
    text.innerHTML = `<i class="fas fa-info-circle"></i> Password strength: ${strength}`;

    // Update requirements list
    const requirements = [
        { met: checks.length, text: 'At least 8 characters' },
        { met: checks.uppercase, text: 'At least 1 uppercase letter' },
        { met: checks.number, text: 'At least 1 number' },
        { met: checks.special, text: 'At least 1 special character' }
    ];

    const reqList = document.querySelector('.password-requirements');
    if (reqList) {
        reqList.innerHTML = requirements.map(req => 
            `<li class="${req.met ? 'met' : ''}">
                <i class="fas fa-${req.met ? 'check' : 'times'}"></i>
                ${req.text}
            </li>`
        ).join('');
    }
}
            color = 'red';
            width = '25%';
            break;
        case 2:
            strength = 'Fair';
            color = 'orange';
            width = '50%';
            break;
        case 3:
            strength = 'Good';
            color = '#1abc9c';
            width = '75%';
            break;
        case 4:
            strength = 'Strong';
            color = 'green';
            width = '100%';
            break;
    }

    bar.style.width = width;
    bar.style.background = color;
    text.textContent = "Password strength: " + strength;
    text.style.color = color;
}
</script>

<?php include("includes/footer.php"); ?>
