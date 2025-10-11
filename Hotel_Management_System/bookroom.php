<?php
require_once "includes/db.php";
session_start();
$error = $success = '';
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

// Helper: parse dd/mm/yyyy to YYYY-MM-DD
if (!function_exists('parseDateInput')) {
    function parseDateInput($dateStr) {
        $parts = explode('/', $dateStr);
        if (count($parts) === 3) {
            return "{$parts[2]}-{$parts[1]}-{$parts[0]}";
        }
        return null;
    }
}

// POST Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $checkin_raw = trim($_POST['checkin'] ?? '');
    $checkout_raw = trim($_POST['checkout'] ?? '');
    $roomtype = $_POST['roomtype'] ?? '';
    $room_id = intval($_POST['room_id'] ?? 0);
    $selected_services = $_POST['services'] ?? [];
    $guests = $_POST['guests'] ?? [];

    // Convert dates
    $checkin = parseDateInput($checkin_raw);
    $checkout = parseDateInput($checkout_raw);

    // Validate
    if (!$name || !$email || !$phone || !$checkin || !$checkout || !$room_id || empty($guests)) {
        $error = "Please fill required fields and add at least one guest.";
    } elseif (strtotime($checkout) <= strtotime($checkin)) {
        $error = "Checkout must be after checkin.";
    } elseif (strtotime($checkin) < strtotime(date('Y-m-d'))) {
        $error = "Check-in date cannot be in the past.";
    } else {
        // Determine user_id
        if (isset($_SESSION['user_id'])) {
            $user_id = intval($_SESSION['user_id']);
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $user_id = $row['id'];
            } else {
                $hash = password_hash(bin2hex(random_bytes(6)), PASSWORD_DEFAULT);
                $usertype = 'guest';
                $status = 'active';
                $nid_dummy = 'GUEST-' . substr(md5($email . time()),0,8);
                $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,password,usertype,status,nid_passport) VALUES (?,?,?,?,?,?,?)");
                $stmt2->bind_param("sssssss",$name,$email,$phone,$hash,$usertype,$status,$nid_dummy);
                $stmt2->execute();
                $user_id = $stmt2->insert_id;
                $stmt2->close();
            }
            $stmt->close();
        }

        // Fetch room info
        $stmtR = $conn->prepare("SELECT id,room_no,type,price FROM rooms WHERE id = ?");
        $stmtR->bind_param("i",$room_id);
        $stmtR->execute();
        $resR = $stmtR->get_result();
        if ($resR && $resR->num_rows>0) {
            $room = $resR->fetch_assoc();
            $nights = max(1,intval((strtotime($checkout)-strtotime($checkin))/(60*60*24)));
            $room_price = floatval($room['price']);
            $base_total = $nights * $room_price;

            // Calculate services
            $services_total = 0.0;
            if (!empty($selected_services) && is_array($selected_services)) {
                foreach ($selected_services as $sid => $qty) {
                    $sid = intval($sid);
                    $qty = max(0,intval($qty));
                    if ($sid && $qty>0) {
                        $sstmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
                        $sstmt->bind_param("i",$sid);
                        $sstmt->execute();
                        $sr = $sstmt->get_result();
                        if ($sr && $sr->num_rows>0) {
                            $srow = $sr->fetch_assoc();
                            $services_total += floatval($srow['price'])*$qty;
                        }
                        $sstmt->close();
                    }
                }
            }

            $total = $base_total + $services_total;
            $num_guests = count($guests);

            // Insert booking
            $stmtB = $conn->prepare("INSERT INTO bookings (user_id,room_id,checkin,checkout,num_guests,total_amount,status) VALUES (?,?,?,?,?,?,?)");
            $status = 'pending';
            $stmtB->bind_param("iissids",$user_id,$room_id,$checkin,$checkout,$num_guests,$total,$status);

            if ($stmtB->execute()) {
                $booking_id = $stmtB->insert_id;

                // Insert booking services
                if (!empty($selected_services) && is_array($selected_services)) {
                    foreach ($selected_services as $sid => $qty) {
                        $sid = intval($sid);
                        $qty = max(0,intval($qty));
                        if ($sid && $qty>0) {
                            $sstmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
                            $sstmt->bind_param("i",$sid);
                            $sstmt->execute();
                            $sr = $sstmt->get_result();
                            if ($sr && $sr->num_rows>0) {
                                $srow = $sr->fetch_assoc();
                                $line_total = floatval($srow['price'])*$qty;
                                $ins = $conn->prepare("INSERT INTO booking_services (booking_id,service_id,qty,total) VALUES (?,?,?,?)");
                                $ins->bind_param("iiid",$booking_id,$sid,$qty,$line_total);
                                $ins->execute();
                                $ins->close();
                            }
                            $sstmt->close();
                        }
                    }
                }

                // Insert guests info
                foreach ($guests as $g) {
                    $g_name = trim($g['guest_name'] ?? '');
                    $g_email = trim($g['guest_email'] ?? '');
                    $g_phone = trim($g['guest_phone'] ?? '');
                    if ($g_name && $g_email && $g_phone) {
                        $stmtG = $conn->prepare("INSERT INTO booking_guests (booking_id, guest_name, guest_email, guest_phone) VALUES (?,?,?,?)");
                        $stmtG->bind_param("isss",$booking_id,$g_name,$g_email,$g_phone);
                        $stmtG->execute();
                        $stmtG->close();
                    }
                }

                $success = "Booking placed successfully! Booking ID: $booking_id. Total: $".number_format($total,2);
            } else {
                $error = "Booking failed: ".$conn->error;
            }
            $stmtB->close();
        } else {
            $error = "Room not found.";
        }
        $stmtR->close();
    }
}

// Fetch room info for form
$roomData = null;
if ($room_id) {
    $stmt = $conn->prepare("SELECT id,room_no,type,description,price FROM rooms WHERE id = ?");
    $stmt->bind_param("i",$room_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $roomData = $res->fetch_assoc();
    $stmt->close();
}

// Fetch services
$svcRes = $conn->query("SELECT id,name,price FROM services ORDER BY name ASC");
$services = [];
while ($s = $svcRes->fetch_assoc()) $services[] = $s;

include("includes/header.php");
include("includes/navbar.php");
?>

<link rel="stylesheet" href="/hotel_management_system/css/bookroom.css">
<div class="container">
<h2 style="color:#1abc9c;">Book a Room</h2>
<?php if ($error) echo "<div class='error'>{$error}</div>"; ?>
<?php if ($success) echo "<div class='success'>{$success}</div>"; ?>

<form class="bookroom-form" method="post" action="bookroom.php" onsubmit="return prepareBookingDates()">
    <input type="hidden" name="room_id" value="<?= htmlspecialchars($room_id) ?>">

    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($_SESSION['fullname'] ?? '') ?>" required>

    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required>

    <label for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" required>

    <label for="checkin">Check-in Date (dd/mm/yyyy)</label>
    <input type="text" id="checkin" name="checkin" placeholder="dd/mm/yyyy" required>

    <label for="checkout">Check-out Date (dd/mm/yyyy)</label>
    <input type="text" id="checkout" name="checkout" placeholder="dd/mm/yyyy" required>

    <label for="roomtype">Room Type</label>
    <select id="roomtype" name="roomtype" required>
    <?php
    if ($roomData) {
        echo "<option value='".htmlspecialchars($roomData['type'])."'>".htmlspecialchars($roomData['type'])." (".$roomData['room_no'].") - $".$roomData['price']."</option>";
    } else {
        echo "<option value=''>Select a room type</option>";
        $r = $conn->query("SELECT DISTINCT type FROM rooms");
        while ($row = $r->fetch_assoc()) {
            echo "<option>".htmlspecialchars($row['type'])."</option>";
        }
    }
    ?>
    </select>

    <!-- Guest Information -->
    <label>Guests Information</label>
    <div id="guests-container">
        <div class="guest" data-index="0">
            <h4>Guest 1</h4>
            <input type="text" name="guests[0][guest_name]" placeholder="Full Name" required>
            <input type="email" name="guests[0][guest_email]" placeholder="Email" required>
            <input type="text" name="guests[0][guest_phone]" placeholder="Phone" required>
        </div>
    </div>
    <button type="button" onclick="addGuest()">Add Another Guest</button>

    <!-- Services -->
    <?php if (!empty($services)): ?>
        <label>Additional Services</label>
        <div style="border:1px solid #eee; padding:8px; border-radius:6px;">
            <?php foreach ($services as $s): ?>
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                    <label style="flex:1;">
                        <input type="checkbox" name="services[<?= $s['id'] ?>]" value="1" onchange="toggleQty(this, <?= $s['id'] ?>)">
                        <?= htmlspecialchars($s['name']) ?> - $<?= number_format($s['price'],2) ?>
                    </label>
                    <input type="number" name="services_qty[<?= $s['id'] ?>]" id="svc_qty_<?= $s['id'] ?>" value="1" min="1" style="width:72px; display:none;">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <button type="submit">Book Now</button>
</form>
</div>

<script src="/hotel_management_system/js/validation.js"></script>
<script>
let guestIndex = 1;
function addGuest() {
    const container = document.getElementById('guests-container');
    const div = document.createElement('div');
    div.classList.add('guest');
    div.dataset.index = guestIndex;
    div.innerHTML = `
        <h4>Guest ${guestIndex+1}</h4>
        <input type="text" name="guests[${guestIndex}][guest_name]" placeholder="Full Name" required>
        <input type="email" name="guests[${guestIndex}][guest_email]" placeholder="Email" required>
        <input type="text" name="guests[${guestIndex}][guest_phone]" placeholder="Phone" required>
        <button type="button" onclick="this.parentElement.remove()">Remove</button>
    `;
    container.appendChild(div);
    guestIndex++;
}

function toggleQty(checkbox, id) {
    const el = document.getElementById('svc_qty_' + id);
    el.style.display = checkbox.checked ? 'inline-block' : 'none';
}

function prepareBookingDates() {
    const checkin = document.getElementById('checkin').value.trim();
    const checkout = document.getElementById('checkout').value.trim();
    const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
    if (!dateRegex.test(checkin) || !dateRegex.test(checkout)) {
        alert('Please enter dates in dd/mm/yyyy format.');
        return false;
    }

    // services qty mapping
    document.querySelectorAll('input[type="checkbox"][name^="services"]').forEach(cb => {
        const name = cb.getAttribute('name');
        const id = name.match(/\d+/)[0];
        if (cb.checked) {
            const qtyEl = document.getElementById('svc_qty_' + id);
            cb.value = qtyEl ? qtyEl.value : 1;
        } else {
            cb.parentElement.removeChild(cb);
        }
    });

    return true;
}
</script>
<?php include("includes/footer.php"); ?>
