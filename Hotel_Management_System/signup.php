<?php
require_once "includes/db.php";
session_start();
if (isset($_SESSION['usertype'])) { header("Location: /hotel_management_system/index.php"); exit; }

$fullname = $email = $phone = $nid = $password = $usertype = '';
$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $nid = trim($_POST['nid_passport'] ?? '');
    $password = $_POST['password'] ?? '';
    $usertype = $_POST['usertype'] ?? 'guest';

    if (!$fullname || !preg_match("/^[a-zA-Z\s]+$/", $fullname)) $errors['fullname'] = "Invalid full name.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email.";
    if (!$phone || !preg_match("/^[0-9+\-\s]{6,20}$/", $phone)) $errors['phone'] = "Invalid phone.";
    if (!$nid) $errors['nid_passport'] = "NID / Passport is required.";
    if (!$password || strlen($password)<6 || !preg_match("/[A-Z]/",$password) || !preg_match("/[0-9]/",$password) || !preg_match("/[!@#$%^&*(),.?\":{}|<>]/",$password)) $errors['password']="Password must be 6+ chars, include uppercase, number & special char.";
    if (!in_array($usertype,['guest','receptionist'])) $errors['usertype']="Invalid user type.";

    if (empty($errors['email'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s",$email); $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows>0) $errors['email']="Email already registered.";
        $stmt->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $status = ($usertype==='receptionist')?'pending':'active';
        $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,nid_passport,password,usertype,status) VALUES (?,?,?,?,?,?,?)");
        $stmt2->bind_param("sssssss",$fullname,$email,$phone,$nid,$hash,$usertype,$status);
        if ($stmt2->execute()) {
            $success = ($status==='pending') ? "Sign up successful! Receptionist account pending approval." : "Sign up successful! You can now sign in.";
            $fullname=$email=$phone=$nid=$password=''; $usertype='guest';
        } else { $errors['general']="Database error: ".$conn->error; }
        $stmt2->close();
    }
}
?>

<?php include("includes/header.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/auth.css">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-left-content">
                <h2>Join Our</h2>
                <h1>Grand Palace Hotel</h1>
                <div class="hotel-rating">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Sign up to enjoy exclusive member benefits, loyalty rewards, and premium service.</p>
                <div class="auth-features">
                    <div class="feature"><i class="fas fa-concierge-bell"></i><span>24/7 Concierge</span></div>
                    <div class="feature"><i class="fas fa-percent"></i><span>Member Discounts</span></div>
                    <div class="feature"><i class="fas fa-gift"></i><span>Loyalty Rewards</span></div>
                </div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-form-header">
                    <h2>Sign Up</h2>
                    <p>Create your account below</p>
                </div>

                <?php if(!empty($errors['general'])): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                <?php if($success): ?>
                    <div class="alert" style="background:#2ecc71;color:#fff;"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="post" class="auth-form">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="fullname" placeholder="Enter your full name" value="<?= htmlspecialchars($fullname) ?>">
                        <?php if(!empty($errors['fullname'])) echo "<span class='error-inline'>{$errors['fullname']}</span>"; ?>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email address" value="<?= htmlspecialchars($email) ?>">
                        <?php if(!empty($errors['email'])) echo "<span class='error-inline'>{$errors['email']}</span>"; ?>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone-alt"></i> Phone Number</label>
                        <input type="text" name="phone" placeholder="Enter your phone number" value="<?= htmlspecialchars($phone) ?>">
                        <?php if(!empty($errors['phone'])) echo "<span class='error-inline'>{$errors['phone']}</span>"; ?>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-id-card"></i> NID / Passport</label>
                        <input type="text" name="nid_passport" placeholder="Enter your ID or passport number" value="<?= htmlspecialchars($nid) ?>">
                        <?php if(!empty($errors['nid_passport'])) echo "<span class='error-inline'>{$errors['nid_passport']}</span>"; ?>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="password-input">
                            <input type="password" name="password" id="password" placeholder="Create your password" value="">
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if(!empty($errors['password'])) echo "<span class='error-inline'>{$errors['password']}</span>"; ?>
                        <div class="password-requirements">
                            <ul>
                                <li><i class="fas fa-check-circle"></i> At least 6 characters</li>
                                <li><i class="fas fa-check-circle"></i> Include uppercase letter</li>
                                <li><i class="fas fa-check-circle"></i> Include number</li>
                                <li><i class="fas fa-check-circle"></i> Include special character</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> User Type</label>
                        <select name="usertype" class="form-select">
                            <option value="guest" <?= $usertype==='guest'?'selected':'' ?>>Guest</option>
                            <option value="receptionist" <?= $usertype==='receptionist'?'selected':'' ?>>Receptionist</option>
                        </select>
                        <?php if(!empty($errors['usertype'])) echo "<span class='error-inline'>{$errors['usertype']}</span>"; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </div>

                    <div class="form-links">
                        <p>Already have an account? <a href="signin.php">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('.password-toggle');
    const passwordInput = document.querySelector('#password');
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
});
</script>

<?php include("includes/footer.php"); ?>
