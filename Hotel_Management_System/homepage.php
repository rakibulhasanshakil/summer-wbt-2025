<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Management System - Home</title>
    <link rel="stylesheet" href="homepage.css">
    <style>
        .nav-main { display: flex; justify-content: space-between; align-items: center; }
        .nav-links a { margin-right: 18px; }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropbtn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #f3eeeeff;
            padding: 0 12px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: #fff;
            min-width: 160px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            z-index: 1;
            border-radius: 6px;
        }
        .dropdown-content a {
            color: #2d3e50;
            padding: 12px 18px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {
            background: #eafaf1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <h1>Grand Palace Hotel</h1>
        <p>“Step into Comfort, Stay with Elegance"</p>
    </header>
    <nav class="nav-main">
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="bookroom.php">Book a Room</a>
            <a href="signin.php">Sign In</a>
        </div>
        <div class="dropdown">
            <button class="dropbtn">&#8942;</button>
            <div class="dropdown-content">
                <a href="rooms.php">Rooms</a>
                <a href="customers.php">Customers</a>
                <a href="staff.php">Staff</a>
                <a href="signout.php">Sign Out</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2>About Our System</h2>
        <p>
            We are pleased to help you manage bookings, rooms, customers, and staff with ease. Streamline your workflow and enhance guest satisfaction with our user-friendly platform.
        </p>
        <div class="features">
            <div class="feature">
                <h3>Easy Booking</h3>
                <p>Quickly book rooms and manage reservations with just a few clicks.</p>
            </div>
            <div class="feature">
                <h3>Room Management</h3>
                <p>View, add, and update room details and availability in real time.</p>
            </div>
            <div class="feature">
                <h3>Customer & Staff</h3>
                <p>Maintain customer records and manage staff efficiently.</p>
            </div>
        </div>
    </div>
</body>
</html>

