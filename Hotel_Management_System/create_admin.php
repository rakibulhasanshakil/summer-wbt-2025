<?php
// create_admin.php
// Put this file in your project root (e.g. C:\xampp\htdocs\hotel_management_system\create_admin.php)
// Run it once by visiting: http://localhost/hotel_management_system/create_admin.php
// After successful run delete this file for security.

try {
    // DB settings — adjust if your includes/db.php uses different path
    require_once __DIR__ . '/includes/db.php'; // make sure this file exists and defines $conn (mysqli)
} catch (Exception $e) {
    die("Cannot load DB connection. Make sure includes/db.php exists and defines \$conn.");
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Database connection not found in includes/db.php. \$conn must be a mysqli object.");
}

$admin_email = 'admin@hotel.com';
$admin_password_plain = 'admin';
$admin_fullname = 'System Admin';
$admin_phone = '0123456789';
$admin_usertype = 'admin';

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    echo "Admin already exists. You can log in with: <strong>{$admin_email}</strong> and password <strong>{$admin_password_plain}</strong> (if that password was created earlier).";
    $stmt->close();
    exit;
}
$stmt->close();

// Insert admin with secure password hash
$hash = password_hash($admin_password_plain, PASSWORD_DEFAULT);

$stmt2 = $conn->prepare("INSERT INTO users (fullname, email, phone, password, usertype, status) VALUES (?, ?, ?, ?, ?, 'active')");
$stmt2->bind_param("sssss", $admin_fullname, $admin_email, $admin_phone, $hash, $admin_usertype);

if ($stmt2->execute()) {
    echo "✅ Admin user created successfully.<br>";
    echo "Email: <strong>{$admin_email}</strong><br>";
    echo "Password: <strong>{$admin_password_plain}</strong><br>";
    echo "Please delete this file (create_admin.php) now for security.";
} else {
    echo "Failed to create admin: " . htmlspecialchars($stmt2->error);
}
$stmt2->close();
$conn->close();
