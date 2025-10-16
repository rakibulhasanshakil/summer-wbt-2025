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
<?php include("includes/header.php");  ?>
<link rel="stylesheet" href="/hotel_management_system/css/profile.css">

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <h2 class="profile-title">My Profile</h2>
        <p class="profile-subtitle"><?php echo htmlspecialchars($user['usertype']); ?> Account</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="profile.php" class="profile-form" onsubmit="return confirm('Save changes to profile?');">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
        </div>

        <div class="form-group">
            <label>NID / Passport Number</label>
            <input type="text" name="nid_passport" value="<?php echo htmlspecialchars($user['nid_passport']); ?>" required>
        </div>

        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <button type="submit" class="save-button">Save Changes</button>
    </form>

    <div class="delete-form">
        <h3 class="delete-heading">Delete Account</h3>
        <p class="delete-warning">Warning: This action is permanent and cannot be undone. All your data, including booking history and personal information, will be permanently deleted.</p>
        
        <form method="post" action="profile.php" onsubmit="return confirm('Are you sure you want to DELETE your account? This cannot be undone.');">
            <input type="hidden" name="delete_account" value="1">
            <button type="submit" class="delete-button">Delete My Account</button>
        </form>
    </div>
</div>
<?php include("includes/footer.php"); ?>
