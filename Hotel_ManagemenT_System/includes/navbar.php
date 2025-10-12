<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$usertype = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : null;
?>
<nav class="nav-main">
<div class="nav-links">
<a href="/hotel_management_system/index.php">Home</a>
<a href="/hotel_management_system/rooms.php">Rooms</a>
<a href="/hotel_management_system/bookroom.php">Book a Room</a>
<?php if (!$usertype): ?>
    <a href="/hotel_management_system/signin.php">Sign In</a>
    <a href="/hotel_management_system/signup.php">Sign Up</a>
<?php else: ?>
    <?php if ($usertype === 'admin'): ?>
        <a href="/hotel_management_system/dashboards/dashboard_admin.php">Dashboard</a>
    <?php elseif ($usertype === 'receptionist'): ?>
        <a href="/hotel_management_system/dashboards/dashboard_receptionist.php">Dashboard</a>
    <?php else: ?>
        <a href="/hotel_management_system/dashboards/dashboard_guest.php">Dashboard</a>
    <?php endif; ?>
    <a href="/hotel_management_system/profile.php">Profile</a>
    <a href="/hotel_management_system/signout.php">Sign Out</a>
<?php endif; ?>
</div>
</nav>
