<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid black; width: 800px; margin: auto; }
        .header, .footer { border-bottom: 1px solid black; padding: 10px; }
        .footer { border-top: 1px solid black; border-bottom: none; text-align: center; font-size: 12px; padding: 5px; }
        .nav { float: right; }
        .content { padding: 20px; display: flex; }
        .sidebar { width: 200px; border-right: 1px solid black; padding-right: 10px; }
        .main { padding-left: 20px; }
        table { border-collapse: collapse; }
        td { padding: 5px; }
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
                    <legend><b>EDIT PROFILE</b></legend>
                    <form method="post" action="">
                        <table>
                            <tr><td>Name</td><td>: <input type="text" name="name" value="Shakil"></td></tr>
                            <tr><td>Email</td><td>: <input type="email" name="email" value="shakils@gmail.com"></td></tr>
                            <tr>
                                <td>Gender</td>
                                <td>: 
                                    <input type="radio" name="gender" value="Male" checked> Male
                                    <input type="radio" name="gender" value="Female"> Female
                                    <input type="radio" name="gender" value="Other"> Other
                                </td>
                            </tr>
                            <tr><td>Date of Birth</td><td>: <input type="date" name="dob" value="1999-12-31"></td></tr>
                        </table>
                        <br>
                        <input type="submit" value="Submit">
                    </form>
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
