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
?>
<link rel="stylesheet" href="/hotel_management_system/css/manage_bookings.css">

<div class="container">
    <h2 class="page-title">Manage Bookings</h2>

    <?php if($error) echo "<div class='alert alert-error'>{$error}</div>"; ?>
    <?php if($success) echo "<div class='alert alert-success'>{$success}</div>"; ?>

    <div class="table-responsive">
        <table class="booking-table">
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

                // Set status class
                $status_class = '';
                switch($row['status']){
                    case 'pending': $status_class = 'pending'; break;
                    case 'checked-in': $status_class = 'checked-in'; break;
                    case 'checked-out': $status_class = 'checked-out'; break;
                    case 'cancelled': $status_class = 'cancelled'; break;
                }
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['type']." (".$row['room_no'].")") ?></td>
                    <td><?= fmtDateDisplay($row['checkin']) ?></td>
                    <td><?= fmtDateDisplay($row['checkout']) ?></td>
                    <td><span class="status <?= $status_class ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td>
                        <?php if($row['status'] === 'pending'): ?>
                            <form method="post" class="action-form">
                                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="checkin" class="btn btn-checkin">Check-in</button>
                                <button type="submit" name="action" value="cancel" class="btn btn-cancel">Cancel</button>
                            </form>
                        <?php elseif($row['status'] === 'checked-in'): ?>
                            <form method="post" class="action-form">
                                <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="checkout" class="btn btn-checkout">Check-out</button>
                            </form>
                        <?php else: ?>
                            <span class="no-action">No actions</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
