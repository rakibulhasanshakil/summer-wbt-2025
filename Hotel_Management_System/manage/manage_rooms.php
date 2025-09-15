<?php
require_once "../includes/db.php";
session_start();
if (!isset($_SESSION['usertype']) || $_SESSION['usertype'] !== 'admin') {
    header("Location: /hotel_management_system/signin.php");
    exit;
}

$error = $success = '';
// handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_no = trim($_POST['room_no'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $id = intval($_POST['id'] ?? 0);

    if (!$room_no || !$type || !$price) $error = "Room number, type and price required.";
    else {
        if ($id) {
            $stmt = $conn->prepare("UPDATE rooms SET room_no=?, type=?, description=?, price=?, status=? WHERE id=?");
            $stmt->bind_param("sssdis",$room_no,$type,$desc,$price,$status,$id);
            if ($stmt->execute()) $success = "Room updated.";
            else $error = "Update failed.";
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO rooms (room_no,type,description,price,status) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssds",$room_no,$type,$desc,$price,$status);
            if ($stmt->execute()) $success = "Room added.";
            else $error = "Insert failed.";
            $stmt->close();
        }
    }
} elseif (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $stmt->close();
}

include("../includes/header.php");
include("../includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/rooms.css">
<div class="container">
  <h2>Manage Rooms</h2>
  <?php if ($error) echo "<div class='error'>{$error}</div>"; ?>
  <?php if ($success) echo "<div class='success'>{$success}</div>"; ?>

  <div style="display:flex; gap:20px;">
    <div style="flex:1">
      <h3>Add / Edit Room</h3>
      <form method="post" action="manage_rooms.php">
        <input type="hidden" name="id" id="room_id" value="">
        <label>Room No</label><input name="room_no" id="room_no" required>
        <label>Type</label><input name="type" id="type" required>
        <label>Description</label><textarea name="description" id="description"></textarea>
        <label>Price</label><input name="price" id="price" type="number" step="0.01" required>
        <label>Status</label>
        <select name="status" id="status"><option value="available">Available</option><option value="occupied">Occupied</option><option value="maintenance">Maintenance</option></select>
        <button type="submit">Save Room</button>
      </form>
    </div>

    <div style="flex:2">
      <h3>All Rooms</h3>
      <table style="width:100%; border-collapse:collapse;">
        <thead><tr><th>ID</th><th>No</th><th>Type</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php
            $res = $conn->query("SELECT id,room_no,type,price,status FROM rooms ORDER BY id DESC");
            while ($r = $res->fetch_assoc()) {
              echo "<tr style='border-top:1px solid #eee;'><td>{$r['id']}</td><td>{$r['room_no']}</td><td>{$r['type']}</td><td>{$r['price']}</td><td>{$r['status']}</td><td><a href='#' onclick='editRoom(".json_encode($r).")'>Edit</a> | <a href='?delete={$r['id']}' onclick='return confirm(\"Delete room?\")'>Delete</a></td></tr>";
            }
          ?>
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
