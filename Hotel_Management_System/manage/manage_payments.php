<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || !in_array($_SESSION['usertype'],['admin','receptionist'])) {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
include("../includes/header.php");
include("../includes/navbar.php");

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $method = trim($_POST['method'] ?? 'cash');
    if ($booking_id && $amount>0) {
        $stmt = $conn->prepare("INSERT INTO payments (booking_id,amount,method) VALUES (?,?,?)");
        $stmt->bind_param("dds",$booking_id,$amount,$method);
        $stmt->execute();
        $stmt->close();
    }
}
?>
<div class="container">
  <h2>Payments</h2>
  <div class="list">
    <h3>Record Payment</h3>
    <form method="post">
      <label>Booking ID</label><input name="booking_id" required>
      <label>Amount</label><input name="amount" type="number" step="0.01" required>
      <label>Method</label><input name="method" value="cash">
      <button type="submit">Record</button>
    </form>

    <h3 style="margin-top:18px;">All Payments</h3>
    <table style="width:100%; border-collapse:collapse;">
      <thead><tr><th>ID</th><th>Booking</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
      <tbody>
      <?php
        $res = $conn->query("SELECT p.id,p.booking_id,p.amount,p.method,p.paid_at FROM payments p ORDER BY paid_at DESC");
        while ($p = $res->fetch_assoc()) {
          echo "<tr style='border-top:1px solid #eee;'><td>{$p['id']}</td><td>{$p['booking_id']}</td><td>{$p['amount']}</td><td>{$p['method']}</td><td>{$p['paid_at']}</td></tr>";
        }
      ?>
      </tbody>
    </table>
  </div>
</div>
<?php include("../includes/footer.php"); ?>
