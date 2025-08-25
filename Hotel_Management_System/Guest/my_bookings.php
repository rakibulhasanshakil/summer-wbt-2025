<?php
session_start();
include("../includes/database.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guest') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$bookings = $conn->query("
    SELECT b.id, r.room_number, r.type, b.check_in, b.check_out, b.status
    FROM bookings b
    JOIN rooms r ON b.room_id=r.id
    WHERE b.user_id=$user_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
</head>
<body>
    <h2>My Bookings</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Booking ID</th>
            <th>Room Number</th>
            <th>Type</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Status</th>
        </tr>
        <?php while($b = $bookings->fetch_assoc()): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= $b['room_number'] ?></td>
            <td><?= $b['type'] ?></td>
            <td><?= $b['check_in'] ?></td>
            <td><?= $b['check_out'] ?></td>
            <td><?= $b['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
