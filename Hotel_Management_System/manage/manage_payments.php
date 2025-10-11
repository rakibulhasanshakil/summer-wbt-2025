<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'receptionist') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}

$error = $success = '';

// Handle Add Payment Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $status = $_POST['status'] ?? 'pending';

    if (!$booking_id || $amount <= 0) {
        $error = "Please select a valid booking and enter a valid amount.";
    } else {
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, status, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ids", $booking_id, $amount, $status);
        if ($stmt->execute()) {
            $success = "Payment record added successfully.";
        } else {
            $error = "Failed to add payment: " . $conn->error;
        }
        $stmt->close();
    }
}

include("../includes/header.php");
include("../includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/manage_payments.css">

<div class="container">
    <h2 class="dashboard-title">Payments</h2>

    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Add Payment Form -->
    <div class="add-payment-form">
        <h3>Add New Payment</h3>
        <form method="post" action="">
            <input type="hidden" name="add_payment" value="1">
            <label for="booking_id">Booking</label>
            <select name="booking_id" id="booking_id" required>
                <option value="">Select Booking</option>
                <?php
                $bookings = $conn->query("SELECT b.id AS booking_id, u.fullname, r.room_no, r.type 
                                          FROM bookings b
                                          JOIN users u ON b.user_id=u.id
                                          JOIN rooms r ON b.room_id=r.id
                                          ORDER BY b.id DESC");
                while($b = $bookings->fetch_assoc()) {
                    echo "<option value='{$b['booking_id']}'>ID {$b['booking_id']} - {$b['fullname']} ({$b['type']} {$b['room_no']})</option>";
                }
                ?>
            </select>

            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" step="0.01" min="0" required>

            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
            </select>

            <button type="submit">Add Payment</button>
        </form>
    </div>

    <!-- Payments Table -->
    <table class="payments-table">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Booking ID</th>
                <th>Guest Name</th>
                <th>Room</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $q = "SELECT p.id AS payment_id, b.id AS booking_id, u.fullname, r.room_no, r.type, p.amount, p.status
              FROM payments p
              JOIN bookings b ON p.booking_id=b.id
              JOIN users u ON b.user_id=u.id
              JOIN rooms r ON b.room_id=r.id
              ORDER BY p.id DESC";
        $res = $conn->query($q);
        if($res && $res->num_rows>0):
            while($row = $res->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['payment_id'] ?></td>
                <td><?= $row['booking_id'] ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['type']." (".$row['room_no'].")") ?></td>
                <td>$<?= number_format($row['amount'],2) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <?php if($row['status'] === 'pending'): ?>
                        <a href="process_payment.php?id=<?= $row['payment_id'] ?>" class="btn-pay">Mark Paid</a>
                    <?php else: ?>
                        <span class="paid-text">Paid</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php 
            endwhile;
        else: ?>
            <tr><td colspan="7" class="no-data">No payments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>
