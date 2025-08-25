<?php
session_start();
include("../includes/database.php");

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle add room
if(isset($_POST['add'])) {
    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    $conn->query("INSERT INTO rooms (room_number, type, price) VALUES ('$room_number','$type','$price')");
}

// Handle delete room
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM rooms WHERE id=$id");
}

// Fetch rooms
$rooms = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Rooms</title>
</head>
<body>
    <h2>Manage Rooms</h2>

    <h3>Add Room</h3>
    <form method="POST">
        Room Number: <input type="text" name="room_number" required>
        Type: <input type="text" name="type" required>
        Price: <input type="number" name="price" required>
        <button type="submit" name="add">Add Room</button>
    </form>

    <h3>Existing Rooms</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Room Number</th>
            <th>Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($room = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= $room['id'] ?></td>
            <td><?= $room['room_number'] ?></td>
            <td><?= $room['type'] ?></td>
            <td><?= $room['price'] ?></td>
            <td><?= $room['status'] ?></td>
            <td><a href="?delete=<?= $room['id'] ?>">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
