<?php
session_start();
include("../includes/database.php");

// Only guests can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guest') {
    header("Location: ../login.php");
    exit();
}

// Fetch available rooms
$rooms = $conn->query("SELECT * FROM rooms WHERE status='available'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Rooms</title>
</head>
<body>
    <h2>Available Rooms</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Room Number</th>
            <th>Type</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while($room = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= $room['room_number'] ?></td>
            <td><?= $room['type'] ?></td>
            <td><?= $room['price'] ?></td>
            <td>
                <a href="book_room.php?room_id=<?= $room['id'] ?>">Book Now</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
