<?php
require_once "includes/db.php";
session_start();

if (!isset($_SESSION['usertype'])) {
    header("Location: signin.php");
    exit;
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($booking_id <= 0) {
    die("Invalid booking ID.");
}

// Booking main info
$sql = "SELECT b.*, 
        u.fullname AS customer_name, u.email, u.phone, u.nid_passport,
        r.room_no, r.type AS room_type, r.price AS room_price
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) die("Booking not found.");

// Services
$sqlS = "SELECT s.name, s.price, bs.qty, bs.total
         FROM booking_services bs
         JOIN services s ON bs.service_id = s.id
         WHERE bs.booking_id = ?";
$stmt = $conn->prepare($sqlS);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Payments
$sqlP = "SELECT amount, method, paid_at FROM payments WHERE booking_id = ?";
$stmt = $conn->prepare($sqlP);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Format dates
$checkin = fmtDateDisplay($booking['checkin']);
$checkout = fmtDateDisplay($booking['checkout']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Booking Invoice #<?php echo $booking_id; ?></title>
<link rel="stylesheet" href="/hotel_management_system/css/invoice.css">
<style>
body { font-family: Arial, sans-serif; margin: 30px; }
.invoice-box {
  max-width: 800px; margin: auto; padding: 20px; border: 1px solid #eee; box-shadow: 0 0 10px #ddd;
}
.header { text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ddd; padding: 8px; text-align:left; }
th { background: #f7f7f7; }
.total { font-weight:bold; text-align:right; }
.print-btn { background:#2ecc71; color:#fff; padding:8px 14px; border:none; border-radius:4px; cursor:pointer; }
.print-btn:hover { background:#27ae60; }
</style>
</head>
<body>
<div class="invoice-box">
  <div class="header">
    <h2>🏨 Hotel Management System</h2>
    <h3>Booking Invoice #<?php echo $booking_id; ?></h3>
  </div>

  <h4>Customer Details</h4>
  <table>
    <tr><th>Name</th><td><?php echo htmlspecialchars($booking['customer_name']); ?></td></tr>
    <tr><th>Email</th><td><?php echo htmlspecialchars($booking['email']); ?></td></tr>
    <tr><th>Phone</th><td><?php echo htmlspecialchars($booking['phone']); ?></td></tr>
    <tr><th>NID/Passport</th><td><?php echo htmlspecialchars($booking['nid_passport']); ?></td></tr>
  </table>

  <h4>Room Details</h4>
  <table>
    <tr><th>Room No</th><td><?php echo htmlspecialchars($booking['room_no']); ?></td></tr>
    <tr><th>Type</th><td><?php echo htmlspecialchars($booking['room_type']); ?></td></tr>
    <tr><th>Price per Night</th><td>$<?php echo number_format($booking['room_price'], 2); ?></td></tr>
    <tr><th>Check-in</th><td><?php echo $checkin; ?></td></tr>
    <tr><th>Check-out</th><td><?php echo $checkout; ?></td></tr>
  </table>

  <?php if (!empty($services)) { ?>
  <h4>Services</h4>
  <table>
    <tr><th>Service</th><th>Price</th><th>Qty</th><th>Total</th></tr>
    <?php foreach ($services as $s): ?>
      <tr>
        <td><?php echo htmlspecialchars($s['name']); ?></td>
        <td>$<?php echo number_format($s['price'],2); ?></td>
        <td><?php echo $s['qty']; ?></td>
        <td>$<?php echo number_format($s['total'],2); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <?php } ?>

  <h4>Payment Summary</h4>
  <table>
    <tr><th>Status</th><td><?php echo ucfirst($booking['status']); ?></td></tr>
    <tr><th>Total Amount</th><td>$<?php echo number_format($booking['total_amount'], 2); ?></td></tr>
    <?php if (!empty($payments)) { ?>
      <tr><th colspan="2">Payments</th></tr>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td><?php echo fmtDateDisplay(substr($p['paid_at'],0,10)); ?> (<?php echo htmlspecialchars($p['method']); ?>)</td>
          <td>$<?php echo number_format($p['amount'],2); ?></td>
        </tr>
      <?php endforeach; ?>
    <?php } ?>
  </table>

  <p style="text-align:center; margin-top:20px;">
    <button class="print-btn" onclick="window.print()">🖨 Print Invoice</button>
  </p>
</div>
</body>
</html>
