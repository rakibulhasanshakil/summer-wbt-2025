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
<?php include("includes/navbar.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/signin.css">

<div class="container">
    <h2>Reset Password</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post" id="resetForm">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label>New Password</label>
        <input type="password" name="newpass" id="newpass" required oninput="checkStrength()">
        <div id="strength-bar" style="height:5px;background:#ccc;border-radius:4px;margin:6px 0;">
            <div id="strength" style="height:100%;width:0%;background:red;border-radius:4px;transition:width 0.3s;"></div>
        </div>
        <small id="strength-text" style="display:block;margin-bottom:10px;color:#777;">Password strength: weak</small>

        <label>Confirm Password</label>
        <input type="password" name="conf" required>

        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>
</div>

<script>
function checkStrength() {
    const pass = document.getElementById('newpass').value;
    const bar = document.getElementById('strength');
    const text = document.getElementById('strength-text');

    let score = 0;
    if (pass.length >= 8) score++;
    if (/[A-Z]/.test(pass)) score++;
    if (/[0-9]/.test(pass)) score++;
    if (/[@$!%*?&]/.test(pass)) score++;

    let strength = '';
    let color = '';
    let width = '0%';

    switch(score) {
        case 0:
        case 1:
            strength = 'Weak';
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
