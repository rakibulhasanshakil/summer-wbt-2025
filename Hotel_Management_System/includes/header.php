<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grand Palace Hotel | Luxury & Comfort</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="/hotel_management_system/css/style.css">
    <link rel="stylesheet" href="/hotel_management_system/css/homepage.css">
</head>
<body>

<!-- ===== Header / Navbar ===== -->
<header class="site-header">
    <div class="navbar-container">
        <div class="logo">
            <a href="/hotel_management_system/index.php"><i class="fas fa-hotel"></i> Grand Palace</a>
        </div>

        <nav class="main-nav">
            <a href="/hotel_management_system/index.php" class="nav-link active">Home</a>
            <a href="/hotel_management_system/rooms.php" class="nav-link">Rooms</a>
            <a href="/hotel_management_system/bookroom.php" class="nav-link">Book Now</a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if(isset($_SESSION['usertype'])): ?>
                    <?php 
                    $dashboard_link = "";
                    switch($_SESSION['usertype']) {
                        case 'admin':
                            $dashboard_link = "/hotel_management_system/dashboards/dashboard_admin.php";
                            break;
                        case 'receptionist':
                            $dashboard_link = "/hotel_management_system/dashboards/dashboard_receptionist.php";
                            break;
                        case 'guest':
                            $dashboard_link = "/hotel_management_system/dashboards/dashboard_guest.php";
                            break;
                    }
                    if($dashboard_link): 
                    ?>
                        <a href="<?php echo $dashboard_link; ?>" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="/hotel_management_system/profile.php" class="nav-link">My Profile</a>
                <a href="/hotel_management_system/signout.php" class="btn-nav btn-outline">Sign Out</a>
            <?php else: ?>
                <a href="/hotel_management_system/signin.php" class="btn-nav">Sign In</a>
                <a href="/hotel_management_system/signup.php" class="btn-nav btn-outline">Sign Up</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
