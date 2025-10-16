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
    $room_id = intval($_POST['room_id'] ?? 0);
    $selected_services = $_POST['services'] ?? [];
    $guests = $_POST['guests'] ?? [];

    // Convert dates
    $checkin = parseDateInput($checkin_raw);
    $checkout = parseDateInput($checkout_raw);

    // Validate
    if (!$name || !$email || !$phone) {
        $error = "⚠️ Please fill your personal details.";
    } elseif (!$checkin || !$checkout) {
        $error = "⚠️ Please select valid check-in and check-out dates.";
    } elseif (!$room_id) {
        $error = "⚠️ Please select a room.";
    } elseif (empty($guests) || !isset($guests[0]['guest_name']) || trim($guests[0]['guest_name']) === '') {
        $error = "⚠️ Please provide at least one guest's information.";
    } elseif (strtotime($checkout) <= strtotime($checkin)) {
        $error = "⚠️ Checkout must be after check-in.";
    } elseif (strtotime($checkin) < strtotime(date('Y-m-d'))) {
        $error = "⚠️ Check-in date cannot be in the past.";
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
                $nid_dummy = 'GUEST-' . substr(md5($email . time()), 0, 8);
                $stmt2 = $conn->prepare("INSERT INTO users (fullname,email,phone,password,usertype,status,nid_passport) VALUES (?,?,?,?,?,?,?)");
                $stmt2->bind_param("sssssss", $name, $email, $phone, $hash, $usertype, $status, $nid_dummy);
                $stmt2->execute();
                $user_id = $stmt2->insert_id;
                $stmt2->close();
            }
            $stmt->close();
        }

        // Fetch room info
        $stmtR = $conn->prepare("SELECT id, room_no, type, price FROM rooms WHERE id = ?");
        $stmtR->bind_param("i", $room_id);
        $stmtR->execute();
        $resR = $stmtR->get_result();

        if ($resR && $resR->num_rows > 0) {
            $room = $resR->fetch_assoc();
            $nights = max(1, intval((strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24)));
            $room_price = floatval($room['price']);
            $base_total = $nights * $room_price;

            // Calculate services total
            $services_total = 0.0;
            if (!empty($selected_services) && is_array($selected_services)) {
                foreach ($selected_services as $sid => $qty) {
                    $sid = intval($sid);
                    $qty = max(0, intval($qty));
                    if ($sid && $qty > 0) {
                        $sstmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
                        $sstmt->bind_param("i", $sid);
                        $sstmt->execute();
                        $sr = $sstmt->get_result();
                        if ($sr && $sr->num_rows > 0) {
                            $srow = $sr->fetch_assoc();
                            $services_total += floatval($srow['price']) * $qty;
                        }
                        $sstmt->close();
                    }
                }
            }

            $total = $base_total + $services_total;
            $num_guests = count($guests);
            $status = 'pending';

            // Correct column order for your table
            $stmtB = $conn->prepare("INSERT INTO bookings (user_id, room_id, checkin, checkout, total_amount, status, num_guests) VALUES (?,?,?,?,?,?,?)");
            $stmtB->bind_param("iissdsi", $user_id, $room_id, $checkin, $checkout, $total, $status, $num_guests);

            if ($stmtB->execute()) {
                $booking_id = $stmtB->insert_id;

                // Insert booking services
                if (!empty($selected_services)) {
                    foreach ($selected_services as $sid => $qty) {
                        $sid = intval($sid);
                        $qty = max(0, intval($qty));
                        if ($sid && $qty > 0) {
                            $sstmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
                            $sstmt->bind_param("i", $sid);
                            $sstmt->execute();
                            $sr = $sstmt->get_result();
                            if ($sr && $sr->num_rows > 0) {
                                $srow = $sr->fetch_assoc();
                                $line_total = floatval($srow['price']) * $qty;
                                $ins = $conn->prepare("INSERT INTO booking_services (booking_id, service_id, qty, total) VALUES (?,?,?,?)");
                                $ins->bind_param("iiid", $booking_id, $sid, $qty, $line_total);
                                $ins->execute();
                                $ins->close();
                            }
                            $sstmt->close();
                        }
                    }
                }

                // Insert guest info
                foreach ($guests as $g) {
                    $g_name = trim($g['guest_name'] ?? '');
                    $g_email = trim($g['guest_email'] ?? '');
                    $g_phone = trim($g['guest_phone'] ?? '');
                    $g_nid = trim($g['guest_nid'] ?? '');
                    if ($g_name && $g_email && $g_phone) {
                        $stmtG = $conn->prepare("INSERT INTO booking_guests (booking_id, guest_name, guest_email, guest_phone, guest_nid) VALUES (?,?,?,?,?)");
                        $stmtG->bind_param("issss", $booking_id, $g_name, $g_email, $g_phone, $g_nid);
                        $stmtG->execute();
                        $stmtG->close();
                    }
                }

                $success = "✅ Booking placed successfully! Booking ID: $booking_id. Total: $" . number_format($total, 2);
            } else {
                $error = "❌ Booking failed: " . $stmtB->error;
            }
            $stmtB->close();
        } else {
            $error = "❌ Room not found.";
        }
        $stmtR->close();
    }
}

// Fetch all rooms for dropdown
$roomsList = $conn->query("SELECT id, room_no, type, price FROM rooms ORDER BY type ASC, room_no ASC");

// Fetch services
$svcRes = $conn->query("SELECT id,name,price FROM services ORDER BY name ASC");
$services = [];
while ($s = $svcRes->fetch_assoc()) $services[] = $s;

include("includes/header.php");
?>

<link rel="stylesheet" href="./css/bookroom.css">
<!-- Booking Header -->
<section class="booking-header">
    <div class="container">
        <h1>Book Your Stay</h1>
        <p>Experience luxury and comfort at its finest</p>
    </div>
</section>

<div class="booking-container">
    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
    <?php endif; ?>

    <div class="booking-form">
        <form method="post" action="bookroom.php" onsubmit="return prepareBookingDates()">
            <!-- Personal Information Section -->
            <h3><i class="fas fa-user-circle"></i> Personal Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name"
                            value="<?= htmlspecialchars($_SESSION['fullname'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" 
                        value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" 
                        required placeholder="Enter your email address">
                </div>

                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" id="phone" name="phone" 
                        value="<?= htmlspecialchars($_SESSION['phone'] ?? '') ?>" 
                        required placeholder="Enter your phone number">
                </div>
            </div>

            <!-- Booking Details Section -->
            <h3><i class="fas fa-calendar-alt"></i> Booking Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="checkin"><i class="fas fa-calendar-check"></i> Check-in Date</label>
                    <input type="text" id="checkin" name="checkin" 
                        class="datepicker" placeholder="Select check-in date" required readonly>
                </div>

                <div class="form-group">
                    <label for="checkout"><i class="fas fa-calendar-times"></i> Check-out Date</label>
                    <input type="text" id="checkout" name="checkout" 
                        class="datepicker" placeholder="Select check-out date" required readonly>
                </div>

                <div class="form-group span-full">
                    <label for="room_id"><i class="fas fa-bed"></i> Select Your Room</label>
                    <select id="room_id" name="room_id" required onchange="updateBookingSummary()" class="room-select">
                        <option value="">-- Select a Room --</option>
                        <?php while ($room = $roomsList->fetch_assoc()): ?>
                            <?php $selected = ($room_id == $room['id']) ? 'selected' : ''; ?>
                            <option value="<?= $room['id'] ?>" data-price="<?= $room['price'] ?>" <?= $selected ?>>
                                Room <?= htmlspecialchars($room['room_no']) ?> – <?= htmlspecialchars($room['type']) ?> 
                                – $<?= number_format($room['price'],2) ?>/night
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

                <!-- Guests Section -->
            <div class="guest-section">
                <h3><i class="fas fa-users"></i> Guest Information</h3>
                <p class="section-description">Please provide details for all staying guests</p>
                
                <div id="guests-container">
                    <div class="guest-form" data-index="0">
                        <div class="guest-header">
                            <h4><i class="fas fa-user-check"></i> Primary Guest</h4>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-signature"></i> Full Name</label>
                                <input type="text" name="guests[0][guest_name]" class="guest-input" 
                                    placeholder="Enter full name" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="guests[0][guest_email]" class="guest-input" 
                                    placeholder="Enter email address" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone-alt"></i> Phone</label>
                                <input type="text" name="guests[0][guest_phone]" class="guest-input" 
                                    placeholder="Enter phone number" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-id-card"></i> ID/Passport</label>
                                <input type="text" name="guests[0][guest_nid]" class="guest-input" 
                                    placeholder="Enter ID or passport number" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-secondary" onclick="addGuest()">
                    <i class="fas fa-user-plus"></i> Add Additional Guest
                </button>
            </div>

            <!-- Services Section -->
            <?php if (!empty($services)): ?>
            <div class="services-section">
                <h3><i class="fas fa-concierge-bell"></i> Additional Services</h3>
                <p class="section-description">Enhance your stay with our premium services</p>
                
                <div class="services-grid">
                    <?php foreach ($services as $s): ?>
                    <div class="service-item">
                        <div class="service-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="service-details">
                            <h4 class="service-name"><?= htmlspecialchars($s['name']) ?></h4>
                            <div class="service-price">$<?= number_format($s['price'],2) ?> per service</div>
                            </div>
                            <div class="service-controls">
                                <input type="checkbox" name="services[<?= $s['id'] ?>]" value="1" 
                                    onchange="toggleQty(this, <?= $s['id'] ?>); updateBookingSummary();">
                                <input type="number" class="service-quantity" name="services_qty[<?= $s['id'] ?>]" 
                                    id="svc_qty_<?= $s['id'] ?>" value="1" min="1" 
                                    data-price="<?= $s['price'] ?>"
                                    onchange="updateBookingSummary()"
                                    style="display:none;">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-check"></i> Complete Booking
                </button>
            </form>
        </div>

        <!-- Booking Summary Sidebar -->
        <div class="booking-summary">
            <div class="summary-header">
                <h3><i class="fas fa-receipt"></i> Booking Summary</h3>
            </div>
            <div id="summary-details">
                <div class="summary-item">
                    <span>Room Charge</span>
                    <span id="room-charge">$0.00</span>
                </div>
                <div class="summary-item">
                    <span>Number of Nights</span>
                    <span id="num-nights">0</span>
                </div>
                <div class="summary-item">
                    <span>Services</span>
                    <span id="services-total">$0.00</span>
                </div>
                <div class="summary-total">
                    <span>Total Amount</span>
                    <span id="total-amount">$0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script>
let guestIndex = 1;

// Initialize date pickers
const checkinPicker = flatpickr("#checkin", {
    dateFormat: "d/m/Y",
    minDate: "today",
    onChange: function(selectedDates) {
        checkoutPicker.set('minDate', selectedDates[0]);
        updateBookingSummary();
    }
});

const checkoutPicker = flatpickr("#checkout", {
    dateFormat: "d/m/Y",
    minDate: "today",
    onChange: function() {
        updateBookingSummary();
    }
});

function addGuest() {
    const container = document.getElementById('guests-container');
    const div = document.createElement('div');
    div.className = 'guest-form';
    div.dataset.index = guestIndex;
    div.innerHTML = `
        <div class="guest-header">
            <h4>Additional Guest ${guestIndex + 1}</h4>
            <button type="button" class="btn-secondary" onclick="removeGuest(this)">
                <i class="fas fa-times"></i> Remove
            </button>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="guests[${guestIndex}][guest_name]" placeholder="Enter full name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="guests[${guestIndex}][guest_email]" placeholder="Enter email" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="guests[${guestIndex}][guest_phone]" placeholder="Enter phone number" required>
            </div>
            <div class="form-group">
                <label>ID/Passport</label>
                <input type="text" name="guests[${guestIndex}][guest_nid]" placeholder="Enter ID or passport number" required>
            </div>
        </div>
    `;
    container.appendChild(div);
    guestIndex++;
}

function removeGuest(button) {
    button.closest('.guest-form').remove();
}

function toggleQty(checkbox, id) {
    const el = document.getElementById('svc_qty_' + id);
    el.style.display = checkbox.checked ? 'inline-block' : 'none';
    if (!checkbox.checked) {
        el.value = 1;
    }
}

function calculateNights(checkin, checkout) {
    if (!checkin || !checkout) return 0;
    const oneDay = 24 * 60 * 60 * 1000;
    const startDate = parseDate(checkin);
    const endDate = parseDate(checkout);
    return Math.round(Math.abs((startDate - endDate) / oneDay));
}

function parseDate(dateStr) {
    const [day, month, year] = dateStr.split('/');
    return new Date(year, month - 1, day);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function updateBookingSummary() {
    const roomSelect = document.getElementById('room_id');
    const checkin = document.getElementById('checkin').value;
    const checkout = document.getElementById('checkout').value;
    
    // Calculate room charge
    let roomPrice = 0;
    if (roomSelect.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        roomPrice = parseFloat(selectedOption.dataset.price);
    }
    
    // Calculate nights
    const nights = calculateNights(checkin, checkout);
    const roomTotal = roomPrice * nights;
    
    // Calculate services
    let servicesTotal = 0;
    document.querySelectorAll('.service-controls input[type="checkbox"]:checked').forEach(checkbox => {
        const id = checkbox.name.match(/\d+/)[0];
        const qtyInput = document.getElementById(`svc_qty_${id}`);
        const price = parseFloat(qtyInput.dataset.price);
        const qty = parseInt(qtyInput.value);
        servicesTotal += price * qty;
    });
    
    // Update summary
    document.getElementById('room-charge').textContent = formatCurrency(roomTotal);
    document.getElementById('num-nights').textContent = nights;
    document.getElementById('services-total').textContent = formatCurrency(servicesTotal);
    document.getElementById('total-amount').textContent = formatCurrency(roomTotal + servicesTotal);
}

function prepareBookingDates() {
    const checkin = document.getElementById('checkin').value.trim();
    const checkout = document.getElementById('checkout').value.trim();
    const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
    
    if (!dateRegex.test(checkin) || !dateRegex.test(checkout)) {
        alert('Please enter dates in dd/mm/yyyy format.');
        return false;
    }
    
    const startDate = parseDate(checkin);
    const endDate = parseDate(checkout);
    
    if (endDate <= startDate) {
        alert('Check-out date must be after check-in date.');
        return false;
    }
    
    return true;
}

// Initial summary update
document.addEventListener('DOMContentLoaded', updateBookingSummary);
</script>

<?php include("includes/footer.php"); ?>
