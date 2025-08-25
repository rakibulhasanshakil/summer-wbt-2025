<?php
session_start();
include("../includes/database.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if($_GET['action']=='checkin') {
        $conn->query("UPDATE rooms r 
                      JOIN bookings b ON r.id=b.room_id 
                      SET r.status='booked' 
                      WHERE b.id=$id");
    } elseif($_GET['action']=='checkout') {
        $conn->query("UPDATE rooms r 
                      JOIN bookings b ON r.id=b.room_id 
                      SET r.status='available' 
                      WHERE b.id=$id");
    }
}

// Fetch approved bookings
$bookings = $conn->query("
    SELECT b.id, u.username, r.room_number, b.check_in, b.check_out, r.status AS room_status
    FROM bookings b
    JOIN users u ON b.user_id=u.id
    JOIN rooms r ON b.room_id=r.id
    WHERE b.status='approved'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist - Check-in/Check-out</title>
</head>
<body>
    <h2>Check-in / Check-out</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Booking ID</th>
            <th>Guest</th>
            <th>Room</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Room Status</th>
            <th>Action</th>
        </tr>
        <?php while($b = $bookings->fetch_assoc()): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= $b['username'] ?></td>
            <td><?= $b['room_number'] ?></td>
            <td><?= $b['check_in'] ?></td>
            <td><?= $b['check_out'] ?></td>
            <td><?= $b['room_status'] ?></td>
            <td>
                <?php if($b['room_status']=='available'): ?>
                    <a href="?action=checkin&id=<?= $b['id'] ?>">Check-in</a>
                <?php else: ?>
                    <a href="?action=checkout&id=<?= $b['id'] ?>">Check-out</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
