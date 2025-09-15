<?php
require_once "includes/db.php";
session_start();

$error = $success = '';
// If user is logged in, redirect away
if (isset($_SESSION['usertype'])) {
    header("Location: /hotel_management_system/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // server-side validation
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $usertype = $_POST['usertype'] ?? 'guest'; // guest or receptionist

    if (!$fullname || !$email || !$password) {
        $error = "Please fill required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!in_array($usertype, ['guest','receptionist'])) {
        $error = "Invalid user type.";
    } else {
        // Check existing email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,password,usertype) VALUES (?,?,?,?,?)");
            $stmt2->bind_param("sssss",$fullname,$email,$phone,$hash,$usertype);
            if ($stmt2->execute()) {
                $success = "Sign up successful! You can now sign in.";
            } else {
                $error = "Error: " . $conn->error;
            }
            $stmt2->close();
        }
        $stmt->close();
    }
}
?>
<?php include("includes/header.php"); include("includes/navbar.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/signup.css">
<script src="/hotel_management_system/js/validation.js"></script>
<div class="container">
  <h2>Sign Up</h2>
  <?php if ($error) echo "<div class='error'>{$error}</div>"; ?>
  <?php if ($success) echo "<div class='success'>{$success}</div>"; ?>
  <form method="post" action="signup.php" onsubmit="return validateSignup()">
    <label>Full Name</label>
    <input type="text" name="fullname" required>
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Phone</label>
    <input type="text" name="phone" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <label>Sign up as</label>
    <select name="usertype" required>
      <option value="guest">Guest</option>
      <option value="receptionist">Receptionist</option>
    </select>
    <button type="submit">Register</button>
  </form>
</div>
<?php include("includes/footer.php"); ?>
