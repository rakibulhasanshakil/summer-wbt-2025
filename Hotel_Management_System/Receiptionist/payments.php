<?php
session_start();
include("../includes/database.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'receptionist') {
    header("Location: ../login.php");
    exit();
}

// Handle payment
if(isset($_GET['pay']) && isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    $amount = $_GET['amount']; // In real app, fetch dynamically

    $conn->query("INSERT INTO payments (booking_id, amount, status) VALUES ($booking_id, $amount, 'paid')");
}

$payments = $conn->query("
    SELECT p.id, u.username, r.room_number, p.amount, p.status, p.payment_date
    FROM payments p
    JOIN bookings b ON p.booking_id=b.id
    JOIN users u ON b.user_id=u.id
    JOIN rooms r ON b.room_id=r.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Receptionist - Payments</title>
</head>
<body>
    <h2>Payments</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Guest</th>
            <th>Room</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while($p = $payments->fetch_assoc()): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['username'] ?></td>
            <td><?= $p['room_number'] ?></td>
            <td><?= $p['amount'] ?></td>
            <td><?= $p['status'] ?></td>
            <td><?= $p['payment_date'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
