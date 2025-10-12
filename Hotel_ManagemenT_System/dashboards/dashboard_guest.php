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
    <h2 class="page-title">Welcome, <?= htmlspecialchars($_SESSION['fullname'] ?? 'Guest') ?>!</h2>

    <div class="cards">
        <div class="card">
            <div class="card-icon">📄</div>
            <h3>My Bookings</h3>
            <p><a href="/hotel_management_system/manage/manage_bookings.php" class="btn">View</a></p>
        </div>
        <div class="card">
            <div class="card-icon">🏨</div>
            <h3>Book a Room</h3>
            <p><a href="/hotel_management_system/bookroom.php" class="btn">Book Now</a></p>
        </div>
        <div class="card">
            <div class="card-icon">👤</div>
            <h3>Profile</h3>
            <p><a href="/hotel_management_system/profile.php" class="btn">Manage Profile</a></p>
        </div>
    </div>

    <div class="list">
        <h3>Your Recent Bookings</h3>
        <table class="booking-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $uid = intval($_SESSION['user_id']);
            $q = "SELECT b.id,b.checkin,b.checkout,b.status,r.room_no FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.user_id={$uid} ORDER BY b.created_at DESC LIMIT 8";
            $res = $conn->query($q);
            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
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
