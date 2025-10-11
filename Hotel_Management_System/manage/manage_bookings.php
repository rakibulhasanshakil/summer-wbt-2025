<?php
require_once "../includes/db.php";
session_start();

// Allow both admin and receptionist
if (!isset($_SESSION['usertype']) || !in_array($_SESSION['usertype'], ['admin','receptionist'])) {
    header("Location: /hotel_management_system/signin.php");
    exit;
}

$error = $success = '';

// Handle booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];

    if (in_array($action, ['checkin','checkout','cancel'])) {
        $new_status = '';
        switch($action){
            case 'checkin': $new_status = 'checked-in'; break;
            case 'checkout': $new_status = 'checked-out'; break;
            case 'cancel': $new_status = 'cancelled'; break;
        }

        $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
        $stmt->bind_param("si",$new_status,$booking_id);
        if ($stmt->execute()) {
            $success = "Booking #$booking_id status updated to '$new_status'.";
        } else {
            $error = "Failed to update booking: ".$conn->error;
        }
        $stmt->close();
    }
}

include("../includes/header.php");
include("../includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/manage_bookings.css">
<div class="container">
<h2>All Bookings</h2>

<?php if($error) echo "<div class='error'>{$error}</div>"; ?>
<?php if($success) echo "<div class='success'>{$success}</div>"; ?>

<table style="width:100%; border-collapse:collapse;">
<thead>
<tr>
<th>ID</th>
<th>User</th>
<th>Room</th>
<th>Check-in</th>
<th>Check-out</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$q = "SELECT b.id,b.checkin,b.checkout,b.status,u.fullname,r.room_no,r.type
      FROM bookings b
      JOIN users u ON b.user_id=u.id
      JOIN rooms r ON b.room_id=r.id
      ORDER BY b.created_at DESC";
$res = $conn->query($q);
while($row = $res->fetch_assoc()):

    // Set row color based on status
    $row_color = '';
    switch($row['status']){
        case 'pending': $row_color = '#fff3cd'; break;       // yellow
        case 'checked-in': $row_color = '#d4edda'; break;    // green
        case 'checked-out': $row_color = '#cce5ff'; break;   // light blue
        case 'cancelled': $row_color = '#f8d7da'; break;     // red
    }
?>
<tr style="background-color: <?= $row_color ?>;">
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['fullname']) ?></td>
<td><?= htmlspecialchars($row['type']." (".$row['room_no'].")") ?></td>
<td><?= $row['checkin'] ?></td>
<td><?= $row['checkout'] ?></td>
<td><?= $row['status'] ?></td>
<td>
    <form method="post" style="display:inline-block;">
        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
        <?php if($row['status'] === 'pending'): ?>
            <button type="submit" name="action" value="checkin">Check-in</button>
            <button type="submit" name="action" value="cancel">Cancel</button>
        <?php elseif($row['status'] === 'checked-in'): ?>
            <button type="submit" name="action" value="checkout">Check-out</button>
        <?php else: ?>
            <span style="color:#555;">No actions</span>
        <?php endif; ?>
    </form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include("../includes/footer.php"); ?>
