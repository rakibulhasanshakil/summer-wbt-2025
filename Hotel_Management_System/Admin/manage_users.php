<?php
session_start();
include("../includes/database.php");

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle block/unblock action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == "block") {
        $conn->query("UPDATE users SET status='blocked' WHERE id=$id");
    } elseif ($_GET['action'] == "unblock") {
        $conn->query("UPDATE users SET status='active' WHERE id=$id");
    }
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Users</title>
</head>
<body>
    <h2>Manage Users</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($user = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['username'] ?></td>
            <td><?= $user['email'] ?></td>
            <td><?= $user['role'] ?></td>
            <td><?= $user['status'] ?></td>
            <td>
                <?php if($user['status']=='active'): ?>
                    <a href="?action=block&id=<?= $user['id'] ?>">Block</a>
                <?php else: ?>
                    <a href="?action=unblock&id=<?= $user['id'] ?>">Unblock</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
