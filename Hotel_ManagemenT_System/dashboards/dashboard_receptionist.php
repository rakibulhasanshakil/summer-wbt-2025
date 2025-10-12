<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'receptionist') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}
include("../includes/header.php");
include("../includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/dashboard.css">
<div class="container">
<h2>Receptionist Dashboard</h2>
<div class="cards">
<div class="card"><h3>Manage Bookings</h3><p><a href="/hotel_management_system/manage/manage_bookings.php">Bookings</a></p></div>
<div class="card"><h3>Check-in / Check-out</h3><p><a href="/hotel_management_system/manage/manage_bookings.php">Check-in/out</a></p></div>
<div class="card"><h3>Payments</h3><p><a href="/hotel_management_system/manage/manage_payments.php">Payments</a></p></div>
</div>
</div>
<?php include("../includes/footer.php"); ?>
