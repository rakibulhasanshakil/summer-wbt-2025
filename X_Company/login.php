<?php
// Initialize variables
$username = $password = "";
$usernameErr = $passwordErr = "";

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Username validation
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = htmlspecialchars($_POST["username"]);
    }

    // Password validation
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = htmlspecialchars($_POST["password"]);
    }

    // If no errors, process login
    if (empty($usernameErr) && empty($passwordErr)) {
        // Example only — here you would check from database
        if ($username === "admin" && $password === "1234") {
            // Redirect after successful login
            header("Location: dashboard.php");
            exit();
        } else {
            $passwordErr = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid black; width: 800px; margin: auto; }
        .header, .footer { border-bottom: 1px solid black; padding: 10px; }
        .footer { border-top: 1px solid black; border-bottom: none; text-align: center; font-size: 12px; padding: 5px; }
        .nav { float: right; }
        .content { padding: 20px; }
        table { border-collapse: collapse; }
        td { padding: 5px; }
        .error { color: red; font-size: 12px; }
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
            <fieldset>
                <legend><b>LOGIN</b></legend>
                <form method="post" action="">
                    <table>
                        <tr>
                            <td>User Name</td>
                            <td>: <input type="text" name="username" value="<?php echo $username; ?>">
                                <span class="error"><?php echo $usernameErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>: <input type="password" name="password">
                                <span class="error"><?php echo $passwordErr; ?></span>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <input type="checkbox" name="remember" <?php if(isset($_POST['remember'])) echo "checked"; ?>> Remember Me
                    <br><br>
                    <input type="submit" value="Submit">
                    <a href="forgot_password.php">Forgot Password?</a>
                </form>
            </fieldset>
        </div>

        <!-- Footer -->
        <div class="footer">
            Copyright right reserved Rakibul Hasan Shakil &copy; 2017
        </div>
    </div>
</body>
</html>
