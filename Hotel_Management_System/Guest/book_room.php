<?php
session_start();
include("../includes/database.php");

// Only guests can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guest') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $conn->query("INSERT INTO bookings (user_id, room_id, check_in, check_out) 
                  VALUES ($user_id, $room_id, '$check_in', '$check_out')");
    
    $conn->query("UPDATE rooms SET status='booked' WHERE id=$room_id");

    echo "Room booked successfully! <a href='my_bookings.php'>View My Bookings</a>";
    exit();
}

// Fetch room info
$room = $conn->query("SELECT * FROM rooms WHERE id=$room_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Room</title>
</head>
<body>
    <h2>Book Room <?= $room['room_number'] ?> - <?= $room['type'] ?></h2>
    <form method="POST">
        Check-in Date: <input type="date" name="check_in" required><br>
        Check-out Date: <input type="date" name="check_out" required><br>
        <button type="submit">Book Now</button>
    </form>
</body>
</html>
