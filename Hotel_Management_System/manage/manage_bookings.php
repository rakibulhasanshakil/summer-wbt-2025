<?php
require_once "../includes/db.php";
session_start();
$usertype = $_SESSION['usertype'] ?? null;
include("../includes/header.php");
include("../includes/navbar.php");

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

// Only receptionist or admin can update bookings
if ($action && $id && in_array($usertype,['admin','receptionist'])) {
    if ($action==='confirm') {
        $stmt = $conn->prepare("UPDATE bookings SET status='confirmed' WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    } elseif ($action==='cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    } elseif ($action==='checkin') {
        $stmt = $conn->prepare("UPDATE bookings SET status='checked_in' WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    } elseif ($action==='checkout') {
        $stmt = $conn->prepare("UPDATE bookings SET status='checked_out' WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    }
}

?>
<div class="container">
  <h2>Manage Bookings</h2>
  <div class="list">
    <table style="width:100%; border-collapse:collapse;">
      <thead><tr><th>ID</th><th>User</th><th>Room</th><th>Dates</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php
        $filterWhere = "";
        if ($usertype === 'guest') {
            $uid = intval($_SESSION['user_id']);
            $filterWhere = " WHERE b.user_id = {$uid} ";
        }
        $q = "SELECT b.id,b.checkin,b.checkout,b.total_amount,b.status,u.fullname,r.room_no FROM bookings b JOIN users u ON b.user_id=u.id JOIN rooms r ON b.room_id=r.id {$filterWhere} ORDER BY b.created_at DESC";
        $res = $conn->query($q);
        while ($b = $res->fetch_assoc()) {
          echo "<tr style='border-top:1px solid #eee;'><td>{$b['id']}</td><td>".htmlspecialchars($b['fullname'])."</td><td>".htmlspecialchars($b['room_no'])."</td><td>{$b['checkin']} to {$b['checkout']}</td><td>{$b['total_amount']}</td><td>{$b['status']}</td><td>";
          if (in_array($usertype,['admin','receptionist'])) {
            if ($b['status']=='pending') echo "<a href='?action=confirm&id={$b['id']}'>Confirm</a> | <a href='?action=cancel&id={$b['id']}'>Cancel</a> | ";
            if ($b['status']=='confirmed') echo "<a href='?action=checkin&id={$b['id']}'>Check-in</a> | ";
            if ($b['status']=='checked_in') echo "<a href='?action=checkout&id={$b['id']}'>Check-out</a> | ";
            echo "<a href='manage_bookings.php?delete={$b['id']}' onclick='return confirm(\"Delete booking?\")'>Delete</a>";
          } else {
            echo "<a href='manage_bookings.php?cancel={$b['id']}' onclick='return confirm(\"Cancel booking?\")'>Cancel</a>";
          }
          echo "</td></tr>";
        }
      ?>
      </tbody>
    </table>
  </div>
</div>
<?php include("../includes/footer.php"); ?>
