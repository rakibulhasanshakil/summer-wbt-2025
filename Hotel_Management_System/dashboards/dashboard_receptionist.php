<?php
require_once "../includes/db.php";
session_start();

// Only allow receptionist
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'receptionist') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}

include("../includes/header.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/dashboard.css">

<div class="container">
    <h2 class="page-title">Welcome, <?= htmlspecialchars($_SESSION['fullname'] ?? 'Receptionist') ?>!</h2>

    <!-- Dashboard Cards -->
    <div class="cards">
        <div class="card">
            <div class="card-icon">📄</div>
            <h3>Manage Bookings</h3>
            <p><a href="/hotel_management_system/manage/manage_bookings.php" class="btn">View</a></p>
        </div>
        <div class="card">
            <div class="card-icon">🛎️</div>
            <h3>Check-in / Check-out</h3>
            <p><a href="/hotel_management_system/manage/manage_bookings.php" class="btn">Process</a></p>
        </div>
        <div class="card">
            <div class="card-icon">💳</div>
            <h3>Payments</h3>
            <p><a href="/hotel_management_system/manage/manage_payments.php" class="btn">Manage</a></p>
        </div>
      
    </div>

    <!-- Recent Bookings Table -->
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
            $q = "SELECT b.id, b.checkin, b.checkout, b.status, r.room_no, u.fullname 
                  FROM bookings b 
                  JOIN rooms r ON b.room_id = r.id 
                  JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC 
                  LIMIT 8";
            $res = $conn->query($q);

            if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>".htmlspecialchars($row['fullname'])."</td>";
                    echo "<td>".htmlspecialchars($row['room_no'])."</td>";
                    echo "<td>".fmtDateDisplay($row['checkin'])." to ".fmtDateDisplay($row['checkout'])."</td>";
                    echo "<td><span class='status ".strtolower($row['status'])."'>".ucfirst($row['status'])."</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No bookings found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
