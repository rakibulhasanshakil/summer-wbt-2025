<?php
$name = $email = $username = $password = $confirmpassword = $gender = $dob = "";
$nameErr = $emailErr = $usernameErr = $passwordErr = $confirmpasswordErr = $genderErr = $dobErr = "";
$successMsg = "";

if (isset($_POST["reset"])) {
    $name = $email = $username = $password = $confirmpassword = $gender = $dob = "";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,}$/", $_POST["username"])) {
        $usernameErr = "Username must be at least 3 characters, letters/numbers/underscores only";
    } else {
        $username = htmlspecialchars($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } elseif (strlen($_POST["password"]) < 6) {
        $passwordErr = "Password must be at least 6 characters";
    } else {
        $password = $_POST["password"];
    }

    if (empty($_POST["confirmpassword"])) {
        $confirmpasswordErr = "Confirm your password";
    } elseif ($_POST["confirmpassword"] !== $_POST["password"]) {
        $confirmpasswordErr = "Passwords do not match";
    } else {
        $confirmpassword = $_POST["confirmpassword"];
    }

    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = $_POST["gender"];
    }

    if (empty($_POST["dob"])) {
        $dobErr = "Date of Birth is required";
    } else {
        $dob = $_POST["dob"];
        $dobTime = strtotime($dob);
        $today = strtotime(date("Y-m-d"));
        if ($dobTime === false) {
            $dobErr = "Invalid date format";
        } elseif ($dobTime > $today) {
            $dobErr = "Date of Birth cannot be in the future";
        }
    }

    if ($nameErr=="" && $emailErr=="" && $usernameErr=="" && $passwordErr=="" && $confirmpasswordErr=="" && $genderErr=="" && $dobErr=="") {
        $successMsg = "✅ Registration Successful!";
        $name = $email = $username = $password = $confirmpassword = $gender = $dob = "";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { border: 1px solid black; width: 800px; margin: auto; }
        .header, .footer { border-bottom: 1px solid black; padding: 10px; }
        .footer { border-top: 1px solid black; border-bottom: none; text-align: center; font-size: 12px; padding: 5px; }
        .nav { float: right; }
        .content { padding: 20px; }
        table { border-collapse: collapse; }
        td { padding: 5px; vertical-align: middle; }
        input[type=text], input[type=password], input[type=date] { width: 200px; }
        .error { color: red; font-size: 12px; }
        .success { color: green; font-weight: bold; }
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
                <legend><b>REGISTRATION</b></legend>

                <?php if ($successMsg != ""): ?>
                    <p class="success"><?php echo $successMsg; ?></p>
                <?php endif; ?>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <table>
                        <tr>
                            <td>Name</td>
                            <td>
                                : <input type="text" name="name" value="<?php echo $name; ?>">
                                <span class="error">* <?php echo $nameErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>
                                : <input type="text" name="email" value="<?php echo $email; ?>">
                                <span class="error">* <?php echo $emailErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>User Name</td>
                            <td>
                                : <input type="text" name="username" value="<?php echo $username; ?>">
                                <span class="error">* <?php echo $usernameErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>
                                : <input type="password" name="password">
                                <span class="error">* <?php echo $passwordErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Confirm Password</td>
                            <td>
                                : <input type="password" name="confirmpassword">
                                <span class="error">* <?php echo $confirmpasswordErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>
                                : 
                                <input type="radio" name="gender" value="Male" <?php if($gender=="Male") echo "checked"; ?>> Male
                                <input type="radio" name="gender" value="Female" <?php if($gender=="Female") echo "checked"; ?>> Female
                                <input type="radio" name="gender" value="Other" <?php if($gender=="Other") echo "checked"; ?>> Other
                                <span class="error">* <?php echo $genderErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>
                                : <input type="date" name="dob" value="<?php echo $dob; ?>">
                                <span class="error">* <?php echo $dobErr; ?></span>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" value="Submit">
                    <input type="submit" name="reset" value="Reset">
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
