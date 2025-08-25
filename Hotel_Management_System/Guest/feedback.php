<?php
session_start();
include("../includes/database.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guest') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $comments = $_POST['comments'];
    $rating = $_POST['rating'];

    $conn->query("INSERT INTO feedback (user_id, booking_id, comments, rating) 
                  VALUES ($user_id, $booking_id, '$comments', $rating)");

    echo "Feedback submitted successfully!";
    exit();
}

// Fetch user's bookings
$bookings = $conn->query("SELECT * FROM bookings WHERE user_id=$user_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Feedback</title>
</head>
<body>
    <h2>Leave Feedback</h2>
    <form method="POST">
        Booking:
        <select name="booking_id" required>
            <?php while($b = $bookings->fetch_assoc()): ?>
                <option value="<?= $b['id'] ?>">Booking #<?= $b['id'] ?> - Room <?= $b['room_id'] ?></option>
            <?php endwhile; ?>
        </select><br>
        Comments: <textarea name="comments" required></textarea><br>
        Rating: <input type="number" name="rating" min="1" max="5" required><br>
        <button type="submit">Submit Feedback</button>
    </form>
</body>
</html>
