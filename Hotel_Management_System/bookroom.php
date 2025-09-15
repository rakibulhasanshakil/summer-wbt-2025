<?php
require_once "includes/db.php";
session_start();

$error = $success = '';
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // booking via form (guest may book without login but we will associate guest email if signed in)
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $checkin = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';
    $roomtype = $_POST['roomtype'] ?? '';
    $room_id = intval($_POST['room_id'] ?? 0);

    // server-side validation
    if (!$name || !$email || !$checkin || !$checkout || !$room_id) {
        $error = "Please fill required fields.";
    } elseif (strtotime($checkout) <= strtotime($checkin)) {
        $error = "Checkout must be after checkin.";
    } else {
        // if user is logged in, use user id; otherwise create a temporary guest user record
        if (isset($_SESSION['user_id'])) {
            $user_id = intval($_SESSION['user_id']);
        } else {
            // create or find guest by email
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows>0) {
                $row = $res->fetch_assoc();
                $user_id = $row['id'];
            } else {
                $hash = password_hash(bin2hex(random_bytes(6)), PASSWORD_DEFAULT);
                $usertype = 'guest';
                $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,password,usertype) VALUES (?,?,?,?,?)");
                $stmt2->bind_param("sssss",$name,$email,$phone,$hash,$usertype);
                $stmt2->execute();
                $user_id = $stmt2->insert_id;
                $stmt2->close();
            }
            $stmt->close();
        }

        // calculate nights and price
        $stmtR = $conn->prepare("SELECT price FROM rooms WHERE id = ?");
        $stmtR->bind_param("i",$room_id);
        $stmtR->execute();
        $resR = $stmtR->get_result();
        if ($resR && $resR->num_rows>0) {
            $room = $resR->fetch_assoc();
            $nights = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);
            $total = $nights * floatval($room['price']);
            // insert booking
            $stmtB = $conn->prepare("INSERT INTO bookings (user_id,room_id,checkin,checkout,total_amount,status) VALUES (?,?,?,?,?,?)");
            $status = 'pending';
            $stmtB->bind_param("iisdds",$user_id,$room_id,$checkin,$checkout,$total,$status);
            if ($stmtB->execute()) {
                $booking_id = $stmtB->insert_id;
                $success = "Booking placed successfully. Booking ID: $booking_id. Total: $" . number_format($total,2);
                // Optionally change room status to reserved/occupied later when confirmed
            } else {
                $error = "Booking failed: " . $conn->error;
            }
            $stmtB->close();
        } else {
            $error = "Room not found.";
        }
        $stmtR->close();
    }
}

// fetch room info for form
$roomData = null;
if ($room_id) {
    $stmt = $conn->prepare("SELECT id,room_no,type,description,price FROM rooms WHERE id = ?");
    $stmt->bind_param("i",$room_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $roomData = $res->fetch_assoc();
    $stmt->close();
}

include("includes/header.php");
include("includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/bookroom.css">
<div class="container">
  <h2 style="color:#1abc9c;">Book a Room</h2>
  <?php if ($error) echo "<div class='error'>{$error}</div>"; ?>
  <?php if ($success) echo "<div class='success'>{$success}</div>"; ?>

  <form class="bookroom-form" method="post" action="bookroom.php" onsubmit="return validateBooking()">
    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" value="<?php echo isset($_SESSION['fullname'])?htmlspecialchars($_SESSION['fullname']):''; ?>" required>

    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" value="<?php echo isset($_SESSION['email'])?htmlspecialchars($_SESSION['email']):''; ?>" required>

    <label for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" value="<?php echo isset($_SESSION['phone'])?htmlspecialchars($_SESSION['phone']):''; ?>" required>

    <label for="checkin">Check-in Date</label>
    <input type="date" id="checkin" name="checkin" required>

    <label for="checkout">Check-out Date</label>
    <input type="date" id="checkout" name="checkout" required>

    <label for="roomtype">Room Type</label>
    <select id="roomtype" name="roomtype" required>
      <?php
        if ($roomData) {
          echo "<option value='".htmlspecialchars($roomData['type'])."'>".htmlspecialchars($roomData['type'])." (".$roomData['room_no'].") - $".$roomData['price']."</option>";
        } else {
          echo "<option value=''>Select a room type</option>";
          // show distinct types
          $r = $conn->query("SELECT DISTINCT type FROM rooms");
          while ($row = $r->fetch_assoc()) {
            echo "<option>".htmlspecialchars($row['type'])."</option>";
          }
        }
      ?>
    </select>

    <button type="submit">Book Now</button>
  </form>
</div>
<script src="/hotel_management_system/js/validation.js"></script>
<?php include("includes/footer.php"); ?>
