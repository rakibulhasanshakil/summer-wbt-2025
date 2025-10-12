<?php
require_once "includes/db.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
$uid = intval($_SESSION['user_id']);
$error = $success = '';

// Delete account request
if (isset($_POST['delete_account'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i",$uid);
    if ($stmt->execute()) {
        $stmt->close();
        session_unset();
        session_destroy();
        header("Location: /hotel_management_system/index.php");
        exit;
    } else {
        $error = "Failed to delete account.";
        $stmt->close();
    }
}

// Update profile or password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_account'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $nid = trim($_POST['nid_passport'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$fullname) $error = "Full name required.";
    else {
        if ($password) {
            if (strlen($password) < 6) { $error = "Password must be >= 6 chars."; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, nid_passport=?, password=? WHERE id=?");
                $stmt->bind_param("ssssi",$fullname,$phone,$nid,$hash,$uid);
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname=?, phone=?, nid_passport=? WHERE id=?");
            $stmt->bind_param("sssi",$fullname,$phone,$nid,$uid);
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
$stmt = $conn->prepare("SELECT fullname,email,phone,usertype,nid_passport FROM users WHERE id=?");
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
<form method="post" action="profile.php" onsubmit="return confirm('Save changes to profile?');">
    <label>Full Name</label>
    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
    <label>Email (cannot change)</label>
    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
    <label>Phone</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
    <label>NID / Passport</label>
    <input type="text" name="nid_passport" value="<?php echo htmlspecialchars($user['nid_passport']); ?>" required>
    <label>New Password (leave blank to keep)</label>
    <input type="password" name="password" placeholder="New password">
    <button type="submit">Save Profile</button>
</form>

<form method="post" action="profile.php" style="margin-top:14px;" onsubmit="return confirm('Are you sure you want to DELETE your account? This cannot be undone.');">
    <input type="hidden" name="delete_account" value="1">
    <button type="submit" style="background:#c0392b;">Delete Account</button>
</form>
</div>
<?php include("includes/footer.php"); ?>
