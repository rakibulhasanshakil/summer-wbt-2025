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

    // Validation
    if (!$fullname) $errors['fullname'] = "Full name is required.";
    if (!$email) $errors['email'] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";

    if (!$phone) $errors['phone'] = "Phone is required.";

    if (!$nid) $errors['nid_passport'] = "NID / Passport is required.";

    if (!$password) $errors['password'] = "Password is required.";
    elseif (strlen($password) < 6) $errors['password'] = "Password must be at least 6 characters.";

    if (!in_array($usertype, ['guest','receptionist'])) $errors['usertype'] = "Invalid user type.";

    // Check email uniqueness if no email error
    if (empty($errors['email'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) $errors['email'] = "Email already registered.";
        $stmt->close();
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $status = ($usertype === 'receptionist') ? 'pending' : 'active';

        $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,nid_passport,password,usertype,status) VALUES (?,?,?,?,?,?,?)");
        $stmt2->bind_param("sssssss",$fullname,$email,$phone,$nid,$hash,$usertype,$status);
        if ($stmt2->execute()) {
            $success = ($status === 'pending') 
                ? "Sign up successful! Receptionist account is pending admin approval." 
                : "Sign up successful! You can now sign in.";
            // Clear form values
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

<form method="post" action="signup.php">

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
    <input type="password" name="password" value="<?= htmlspecialchars($password) ?>">
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
/* Inline error style */
.error-inline { display:block; color:#c0392b; font-size:0.9em; margin-top:2px; }
</style>

<?php include("includes/footer.php"); ?>
