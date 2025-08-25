<?php
session_start();
include("includes/database.php");

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if($user) {
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = strtolower($user['role']); // normalize role

            // Redirect by role
            switch($_SESSION['role']) {
                case 'admin':
                    header("Location: admin/manage_users.php");
                    break;
                case 'receptionist':
                    header("Location: receptionist/manage_bookings.php");
                    break;
                case 'guest':
                    header("Location: guest/browse_rooms.php");
                    break;
                default:
                    $error = "Role not recognized!";
            }
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "No account found with that email!";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>Login</h2>
<?php if($error != "") echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
Email: <input type="email" name="email" required><br>
Password: <input type="password" name="password" required><br>
<button type="submit">Login</button>
</form>
<p><a href="forgot_password.php">Forgot Password?</a></p>
</body>
</html>
