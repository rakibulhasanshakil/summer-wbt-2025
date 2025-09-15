<?php
require_once "includes/db.php";
session_start();

// If already logged in redirect to appropriate dashboard
if (isset($_SESSION['usertype'])) {
  $u = $_SESSION['usertype'];
  header("Location: /hotel_management_system/dashboards/dashboard_{$u}.php");
  exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, email, password, usertype, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if ($user['status'] !== 'active') {
                $error = "Account is blocked. Contact admin.";
            } elseif (password_verify($password, $user['password'])) {
                // Credentials ok
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
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
<?php include("includes/header.php"); include("includes/navbar.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/signin.css">
<div class="container">
  <div class="container">
    <h2>Sign In</h2>
    <?php if ($error) echo "<div class='error'>{$error}</div>"; ?>
    <form method="post" action="signin.php">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</div>
<?php include("includes/footer.php"); ?>
