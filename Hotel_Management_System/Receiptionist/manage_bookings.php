<?php
session_start();
include("../includes/database.php");

// Only receptionist can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../login.php");
    exit();
}

// Approve or cancel booking
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if($_GET['action']=='approve') {
        $conn->query("UPDATE bookings SET status='approved' WHERE id=$id");
    } elseif($_GET['action']=='cancel') {
        $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$id");
    }
}

// Fetch all bookings
$bookings = $conn->query("
    SELECT b.id, u.username, r.room_number, b.check_in, b.check_out, b.status
    FROM bookings b
    JOIN users u ON b.user_id=u.id
    JOIN rooms r ON b.room_id=r.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist - Manage Bookings</title>
</head>
<body>
    <h2>Manage Bookings</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Guest</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($b = $bookings->fetch_assoc()): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= $b['username'] ?></td>
            <td><?= $b['room_number'] ?></td>
            <td><?= $b['check_in'] ?></td>
            <td><?= $b['check_out'] ?></td>
            <td><?= $b['status'] ?></td>
            <td>
                <?php if($b['status']=='pending'): ?>
                    <a href="?action=approve&id=<?= $b['id'] ?>">Approve</a> |
                    <a href="?action=cancel&id=<?= $b['id'] ?>">Cancel</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
