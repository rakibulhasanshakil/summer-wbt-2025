<?php
require_once "includes/db.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
$uid = intval($_SESSION['user_id']);
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$fullname) $error = "Full name required.";
    else {
        if ($password) {
            if (strlen($password) < 6) { $error = "Password must be >= 6 chars."; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, password=? WHERE id=?");
                $stmt->bind_param("sssi",$fullname,$phone,$hash,$uid);
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=? WHERE id=?");
            $stmt->bind_param("ssi",$fullname,$phone,$uid);
        }
        if (!$error) {
            if ($stmt->execute()) {
                $success = "Profile updated.";
                $_SESSION['fullname'] = $fullname;
            } else {
                $error = "Update failed: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// fetch user
$stmt = $conn->prepare("SELECT fullname,email,phone,usertype FROM users WHERE id=?");
$stmt->bind_param("i",$uid);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();
?>
<?php include("includes/header.php"); include("includes/navbar.php"); ?>
<link rel="stylesheet" href="/hotel_management_system/css/signup.css">
<div class="container">
  <h2>Profile</h2>
  <?php if ($error) echo "<div class='error'>{$error}</div>"; ?>
  <?php if ($success) echo "<div class='success'>{$success}</div>"; ?>
  <form method="post" action="profile.php">
    <label>Full Name</label>
    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
    <label>Email (cannot change)</label>
    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
    <label>Phone</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
    <label>New Password (leave blank to keep)</label>
    <input type="password" name="password" placeholder="New password">
    <button type="submit">Save Profile</button>
  </form>
</div>
<?php include("includes/footer.php"); ?>
