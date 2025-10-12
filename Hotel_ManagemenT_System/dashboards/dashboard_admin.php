<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
include("../includes/header.php");
include("../includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/dashboard.css">

<div class="container">
    <h2 class="page-title">Admin Dashboard</h2>

    <div class="cards">
        <div class="card">
            <div class="card-icon">👥</div>
            <h3>Manage Users</h3>
            <p><a href="/hotel_management_system/manage/manage_users.php" class="btn">Go to Users</a></p>
        </div>
        <div class="card">
            <div class="card-icon">🛏️</div>
            <h3>Manage Rooms</h3>
            <p><a href="/hotel_management_system/manage/manage_rooms.php" class="btn">Go to Rooms</a></p>
        </div>
        <div class="card">
            <div class="card-icon">📊</div>
            <h3>Reports</h3>
            <p><a href="/hotel_management_system/manage/manage_bookings.php" class="btn">View Bookings</a></p>
        </div>
    </div>

    <div class="list">
        <h3>Recent Bookings</h3>
        <table class="booking-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $q = "SELECT b.id,b.checkin,b.checkout,b.status,u.fullname,r.room_no 
                  FROM bookings b 
                  JOIN users u ON b.user_id=u.id 
                  JOIN rooms r ON b.room_id=r.id 
                  ORDER BY b.created_at DESC 
                  LIMIT 8";
            $res = $conn->query($q);
            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>".htmlspecialchars($row['fullname'])."</td>";
                echo "<td>".htmlspecialchars($row['room_no'])."</td>";
                echo "<td>".fmtDateDisplay($row['checkin'])." to ".fmtDateDisplay($row['checkout'])."</td>";
                echo "<td><span class='status ".strtolower($row['status'])."'>".ucfirst($row['status'])."</span></td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
