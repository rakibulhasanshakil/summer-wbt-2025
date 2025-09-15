<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'guest') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
include("../includes/header.php");
include("../includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/dashboard.css">
<div class="container">
  <h2>Guest Dashboard</h2>
  <div class="cards">
    <div class="card"><h3>My Bookings</h3><p><a href="/hotel_management_system/manage/manage_bookings.php">View</a></p></div>
    <div class="card"><h3>Book a Room</h3><p><a href="/hotel_management_system/bookroom.php">Book Now</a></p></div>
    <div class="card"><h3>Leave Feedback</h3><p><a href="/hotel_management_system/manage/manage_bookings.php">Feedback</a></p></div>
  </div>

  <div class="list">
    <h3>Your Recent Bookings</h3>
    <table style="width:100%; border-collapse:collapse;">
      <thead><tr><th>#</th><th>Room</th><th>Dates</th><th>Status</th></tr></thead>
      <tbody>
      <?php
        $uid = intval($_SESSION['user_id']);
        $q = "SELECT b.id,b.checkin,b.checkout,b.status,r.room_no FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.user_id={$uid} ORDER BY b.created_at DESC LIMIT 8";
        $res = $conn->query($q);
        while ($row = $res->fetch_assoc()) {
          echo "<tr style='border-top:1px solid #eee;'><td>{$row['id']}</td><td>".htmlspecialchars($row['room_no'])."</td><td>{$row['checkin']} to {$row['checkout']}</td><td>{$row['status']}</td></tr>";
        }
      ?>
      </tbody>
    </table>
  </div>
</div>
<?php include("../includes/footer.php"); ?>
