<?php
require_once "includes/db.php";
session_start();

if (isset($_SESSION['usertype'])) {
    header("Location: /hotel_management_system/index.php");
    exit;
}

// Initialize variables
$fullname = $email = $phone = $nid = $password = $usertype = '';
$errors = [];
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $nid = trim($_POST['nid_passport'] ?? '');
    $password = $_POST['password'] ?? '';
    $usertype = $_POST['usertype'] ?? 'guest';

    // ============================
    // 🔒 VALIDATION
    // ============================

    // Full name: letters and spaces only
    if (!$fullname) {
        $errors['fullname'] = "Full name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors['fullname'] = "Full name must contain only letters and spaces.";
    }

    // Email
    if (!$email) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format. Must include '@' and '.'";
    }

    // Phone
    if (!$phone) {
        $errors['phone'] = "Phone is required.";
    } elseif (!preg_match("/^[0-9+\-\s]{6,20}$/", $phone)) {
        $errors['phone'] = "Invalid phone number.";
    }

    // NID / Passport
    if (!$nid) $errors['nid_passport'] = "NID / Passport is required.";

    // Password: at least one uppercase, one number, one special character
    if (!$password) {
        $errors['password'] = "Password is required.";
    } elseif (
        strlen($password) < 6 ||
        !preg_match("/[A-Z]/", $password) ||  // uppercase
        !preg_match("/[0-9]/", $password) ||  // number
        !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password) // special char
    ) {
        $errors['password'] = "Password must be at least 6 chars, include one uppercase letter, one number, and one special character.";
    }

    // User type
    if (!in_array($usertype, ['guest','receptionist'])) {
        $errors['usertype'] = "Invalid user type.";
    }

    // Check email uniqueness
    if (empty($errors['email'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) $errors['email'] = "Email already registered.";
        $stmt->close();
    }

    // ============================
    // ✅ Insert into Database
    // ============================
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $status = ($usertype === 'receptionist') ? 'pending' : 'active';

        $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,nid_passport,password,usertype,status) VALUES (?,?,?,?,?,?,?)");
        $stmt2->bind_param("sssssss",$fullname,$email,$phone,$nid,$hash,$usertype,$status);
        if ($stmt2->execute()) {
            $success = ($status === 'pending') 
                ? "Sign up successful! Receptionist account is pending admin approval." 
                : "Sign up successful! You can now sign in.";
            $fullname = $email = $phone = $nid = $password = '';
            $usertype = 'guest';
        } else {
            $errors['general'] = "Database error: " . $conn->error;
        }
        $stmt2->close();
    }
}
?>

<?php include("includes/header.php"); include("includes/navbar.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/signup.css">

<div class="container">
<h2>Sign Up</h2>

<?php if (!empty($errors['general'])) echo "<div class='error'>{$errors['general']}</div>"; ?>
<?php if ($success) echo "<div class='success'>{$success}</div>"; ?>

<form method="post" action="signup.php" id="signupForm">

    <label>Full Name</label>
    <input type="text" name="fullname" value="<?= htmlspecialchars($fullname) ?>">
    <?php if(!empty($errors['fullname'])) echo "<span class='error-inline'>{$errors['fullname']}</span>"; ?>

    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
    <?php if(!empty($errors['email'])) echo "<span class='error-inline'>{$errors['email']}</span>"; ?>

    <label>Phone</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">
    <?php if(!empty($errors['phone'])) echo "<span class='error-inline'>{$errors['phone']}</span>"; ?>

    <label>NID / Passport</label>
    <input type="text" name="nid_passport" value="<?= htmlspecialchars($nid) ?>" placeholder="NID or Passport no">
    <?php if(!empty($errors['nid_passport'])) echo "<span class='error-inline'>{$errors['nid_passport']}</span>"; ?>

    <label>Password</label>
    <input type="password" name="password" id="password" value="<?= htmlspecialchars($password) ?>">
    <div id="password-strength"></div>
    <?php if(!empty($errors['password'])) echo "<span class='error-inline'>{$errors['password']}</span>"; ?>

    <label>Sign up as</label>
    <select name="usertype">
        <option value="guest" <?= $usertype==='guest'?'selected':'' ?>>Guest</option>
        <option value="receptionist" <?= $usertype==='receptionist'?'selected':'' ?>>Receptionist (needs admin approval)</option>
    </select>
    <?php if(!empty($errors['usertype'])) echo "<span class='error-inline'>{$errors['usertype']}</span>"; ?>

    <button type="submit">Register</button>
</form>
</div>

<style>
.container {
  max-width: 500px;
  margin: 40px auto;
  padding: 25px;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.error-inline { color:#c0392b; font-size:0.9em; margin-top:3px; display:block; }
.success { background:#2ecc71; color:#fff; padding:10px; margin-bottom:10px; border-radius:6px; }
.error { background:#e74c3c; color:#fff; padding:10px; margin-bottom:10px; border-radius:6px; }

#password-strength {
  margin-top:5px;
  font-weight:500;
}
.strength-weak { color:#e74c3c; }
.strength-medium { color:#f39c12; }
.strength-strong { color:#27ae60; }
</style>

<script>
// ============================
// 🔥 Live Password Strength Checker
// ============================
document.getElementById('password').addEventListener('input', function() {
    const pwd = this.value;
    const strengthText = document.getElementById('password-strength');
    let strength = 0;

    if (pwd.length >= 6) strength++;
    if (/[A-Z]/.test(pwd)) strength++;
    if (/[0-9]/.test(pwd)) strength++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(pwd)) strength++;

    switch (strength) {
        case 0:
        case 1:
            strengthText.textContent = "Weak password";
            strengthText.className = "strength-weak";
            break;
        case 2:
        case 3:
            strengthText.textContent = "Medium strength";
            strengthText.className = "strength-medium";
            break;
        case 4:
            strengthText.textContent = "Strong password";
            strengthText.className = "strength-strong";
            break;
    }
});
</script>

<?php include("includes/footer.php"); ?>
