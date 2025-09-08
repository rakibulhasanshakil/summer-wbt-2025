<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid black; width: 800px; margin: auto; }
        .header, .footer { border-bottom: 1px solid black; padding: 10px; }
        .footer { border-top: 1px solid black; border-bottom: none; text-align: center; font-size: 12px; padding: 5px; }
        .nav { float: right; }
        .content { padding: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="Image/logo.png" alt="xCompany" height="40">
            <div class="nav">
                <a href="home.php">Home</a> |
                <a href="login.php">Login</a> |
                <a href="registration.php">Registration</a>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h3>You have been successfully logged out.</h3>
            <p><a href="login.php">Click here to login again</a></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            Copyright right reserved Rakibul Hasan Shakil &copy; 2017
        </div>
    </div>
</body>
</html>
