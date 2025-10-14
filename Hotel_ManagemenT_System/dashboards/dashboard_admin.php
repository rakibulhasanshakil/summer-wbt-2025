<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
include("../includes/header.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/dashboard.css">

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2 class="page-title">Admin Dashboard</h2>
        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?></p>
    </div>

    <div class="stats-grid">
        <?php
        // Get total users count
        $users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE usertype != 'admin'")->fetch_assoc()['count'];
        
        // Get total rooms count
        $rooms_count = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()['count'];
        
        // Get total active bookings
        $active_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")->fetch_assoc()['count'];
        
        // Get total revenue
        $revenue = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'")->fetch_assoc()['total'];
        $revenue = $revenue ? $revenue : 0;
        ?>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-title">Total Users</div>
            <div class="stat-value"><?php echo $users_count; ?></div>
            <div class="stat-description">Registered guests and staff</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-bed"></i>
            </div>
            <div class="stat-title">Total Rooms</div>
            <div class="stat-value"><?php echo $rooms_count; ?></div>
            <div class="stat-description">Available for booking</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-title">Active Bookings</div>
            <div class="stat-value"><?php echo $active_bookings; ?></div>
            <div class="stat-description">Current confirmed bookings</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-title">Total Revenue</div>
            <div class="stat-value">$<?php echo number_format($revenue, 2); ?></div>
            <div class="stat-description">From completed bookings</div>
        </div>
    </div>

    <div class="quick-actions">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-user-cog"></i>
            </div>
            <h3 class="action-title">Manage Users</h3>
            <a href="/hotel_management_system/manage/manage_users.php" class="action-link">
                View Users <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-door-open"></i>
            </div>
            <h3 class="action-title">Manage Rooms</h3>
            <a href="/hotel_management_system/manage/manage_rooms.php" class="action-link">
                View Rooms <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="action-title">View Reports</h3>
            <a href="/hotel_management_system/manage/manage_bookings.php" class="action-link">
                See Reports <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="recent-bookings">
        <div class="section-header">
            <h3 class="section-title">Recent Bookings</h3>
            <a href="/hotel_management_system/manage/manage_bookings.php" class="action-link">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
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
