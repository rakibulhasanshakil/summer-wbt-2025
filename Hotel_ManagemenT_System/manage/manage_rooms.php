<?php
require_once "../includes/db.php";
session_start();

// Admin check
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}

$error = $success = '';

// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_no = trim($_POST['room_no'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $id = intval($_POST['id'] ?? 0);

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'uploads/room_' . time() . '.' . $ext;
            if (!is_dir('../uploads')) mkdir('../uploads', 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $filename)) {
                $image_path = $filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image type. Only JPG, PNG, GIF allowed.";
        }
    }

    if (!$room_no || !$type || !$price) {
        $error = "Room number, type, and price are required.";
    } else {
        if ($id) {
            // If editing, keep previous image if no new upload
            if (!$image_path) {
                $res_img = $conn->query("SELECT image FROM rooms WHERE id=$id");
                if ($res_img && $res_img->num_rows > 0) {
                    $image_path = $res_img->fetch_assoc()['image'];
                }
            }
            $stmt = $conn->prepare("UPDATE rooms SET room_no=?, type=?, description=?, price=?, status=?, image=? WHERE id=?");
            $stmt->bind_param("sssdssi", $room_no, $type, $desc, $price, $status, $image_path, $id);
            $success = $stmt->execute() ? "Room updated successfully." : "Update failed!";
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO rooms (room_no,type,description,price,status,image) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sssdss", $room_no, $type, $desc, $price, $status, $image_path);
            $success = $stmt->execute() ? "Room added successfully." : "Insert failed!";
            $stmt->close();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $success = "Room deleted successfully.";
}

include("../includes/header.php");
include("../includes/navbar.php");
?>

<link rel="stylesheet" href="/hotel_management_system/css/manage_rooms.css">

<div class="container">
    <h2 class="dashboard-title">Manage Rooms</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="manage-wrapper">
        <!-- Add/Edit Form -->
        <div class="form-card">
            <h3>Add / Edit Room</h3>
            <form method="post" action="manage_rooms.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="room_id" value="">
                <label>Room No</label>
                <input name="room_no" id="room_no" required>
                <label>Type</label>
                <input name="type" id="type" required>
                <label>Description</label>
                <textarea name="description" id="description"></textarea>
                <label>Price</label>
                <input name="price" id="price" type="number" step="0.01" required>
                <label>Status</label>
                <select name="status" id="status">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                <label>Room Image</label>
                <input type="file" name="image" accept="image/*">
                <button type="submit" class="btn-save">Save Room</button>
            </form>
        </div>

        <!-- Room List Table -->
        <div class="table-card">
            <h3>All Rooms</h3>
            <table class="rooms-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>No</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $res = $conn->query("SELECT id,room_no,type,description,price,status,image FROM rooms ORDER BY id DESC");
                while ($r = $res->fetch_assoc()):
                    $img = !empty($r['image']) ? $r['image'] : 'images/no-image.jpg';
                ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['room_no']) ?></td>
                        <td><?= htmlspecialchars($r['type']) ?></td>
                        <td><?= htmlspecialchars($r['description']) ?></td>
                        <td>$<?= number_format($r['price'],2) ?></td>
                        <td><?= ucfirst($r['status']) ?></td>
                        <td><img src="/hotel_management_system/<?= $img ?>" style="width:80px;height:50px;object-fit:cover;border-radius:4px;"></td>
                        <td>
                            <button class="btn-edit" onclick='editRoom(<?= json_encode($r) ?>)'>Edit</button>
                            <a class="btn-delete" href='?delete=<?= $r['id'] ?>' onclick='return confirm("Delete this room?")'>Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function editRoom(r){
    document.getElementById('room_id').value = r.id;
    document.getElementById('room_no').value = r.room_no;
    document.getElementById('type').value = r.type;
    document.getElementById('description').value = r.description || '';
    document.getElementById('price').value = r.price;
    document.getElementById('status').value = r.status;
}
</script>

<?php include("../includes/footer.php"); ?>
