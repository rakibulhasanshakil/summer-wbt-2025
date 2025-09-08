<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid black; width: 800px; margin: auto; }
        .header, .footer { border-bottom: 1px solid black; padding: 10px; }
        .footer { border-top: 1px solid black; border-bottom: none; text-align: center; font-size: 12px; padding: 5px; }
        .nav { float: right; }
        .content { padding: 20px; display: flex; }
        .sidebar { width: 200px; border-right: 1px solid black; padding-right: 10px; }
        .main { padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="Image/logo.png" alt="xCompany" height="40">
            <div class="nav">
                Logged in as <a href="view_profile.php">Shakil</a> | <a href="logout.php">Logout</a>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="sidebar">
                <b>Account</b><br><hr>
                <a href="dashboard.php">Dashboard</a><br>
                <a href="view_profile.php">View Profile</a><br>
                <a href="edit_profile.php">Edit Profile</a><br>
                <a href="change_picture.php">Change Profile Picture</a><br>
                <a href="change_password.php">Change Password</a><br>
                <a href="logout.php">Logout</a>
            </div>
            <div class="main">
                <h3>Welcome Shakil</h3>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Copyright right reserved Rakibul Hasan Shakil &copy; 2017
        </div>
    </div>
</body>
</html>
