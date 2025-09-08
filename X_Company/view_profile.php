<!DOCTYPE html>
<html>
<head>
    <title>View Profile</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid black; width: 800px; margin: auto; }
        .header, .footer { border-bottom: 1px solid black; padding: 10px; }
        .footer { border-top: 1px solid black; border-bottom: none; text-align: center; font-size: 12px; padding: 5px; }
        .nav { float: right; }
        .content { padding: 20px; display: flex; }
        .sidebar { width: 200px; border-right: 1px solid black; padding-right: 10px; }
        .main { padding-left: 20px; }
        table { border-collapse: collapse; width: 100%; }
        td { padding: 5px; vertical-align: top; }
        .profile-info { width: 70%; }
        .profile-pic { text-align: center; }
        img { height: 120px; border: 1px solid #ccc; padding: 5px; }
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
                <fieldset>
                    <legend><b>PROFILE</b></legend>
                    <table>
                        <tr>
                            <!-- User Information -->
                            <td class="profile-info">
                                <table>
                                    <tr><td>Name</td><td>: Shakil</td></tr>
                                    <tr><td>Email</td><td>: 22-49462-3@aiub.edu</td></tr>
                                    <tr><td>Gender</td><td>: Male</td></tr>
                                    <tr><td>Date of Birth</td><td>: 19/09/1998</td></tr>
                                </table>
                            </td>

                            <!-- Profile Picture -->
                            <td class="profile-pic">
                                <img src="Image/shakil.jpg" alt="Profile Picture"><br>
                                <a href="change_picture.php">Change</a>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <a href="edit_profile.php">Edit Profile</a>
                </fieldset>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Copyright right reserved Rakibul Hasan Shakil &copy; 2017
        </div>
    </div>
</body>
</html>
