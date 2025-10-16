<?php
require_once "includes/db.php";
session_start();

if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'guest') {
    header("Location: signin.php");
    exit;
}

$booking_id = intval($_GET['booking_id'] ?? 0);
$error = $success = '';

if ($booking_id) {
    // Fetch booking info
    $stmt = $conn->prepare("SELECT b.id, b.total_amount, b.status, r.room_no 
                            FROM bookings b 
                            JOIN rooms r ON b.room_id = r.id 
                            WHERE b.id = ? AND b.user_id = ?");
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $booking = $res->fetch_assoc();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Simple simulated payment update
            $update = $conn->prepare("UPDATE bookings SET status='paid' WHERE id=?");
            $update->bind_param("i", $booking_id);
            $update->execute();
            $success = "✅ Payment successful for Booking #{$booking_id}.";
            $update->close();
        }
    } else {
        $error = "Booking not found.";
    }
    $stmt->close();
} else {
    $error = "Invalid booking ID.";
}

include("includes/header.php");
include("includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/dashboard.css">
<div class="container">
    <h2>Payment</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>

    <?php if (isset($booking) && !$success): ?>
    <div class="card" style="padding:20px;max-width:500px;">
        <p><strong>Booking ID:</strong> <?= $booking['id'] ?></p>
        <p><strong>Room No:</strong> <?= htmlspecialchars($booking['room_no']) ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($booking['total_amount'],2) ?></p>
        <p><strong>Status:</strong> <?= ucfirst($booking['status']) ?></p>

        <?php if (strtolower($booking['status']) === 'confirmed'): ?>
        <form method="post">
            <button type="submit" class="btn" style="background:#1abc9c;color:#fff;">Confirm Payment</button>
        </form>
        <?php else: ?>
            <p><em>Payment not available for this booking.</em></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php include("includes/footer.php"); ?>
