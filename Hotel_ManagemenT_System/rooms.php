<?php
require_once "includes/db.php";
include("includes/header.php");
include("includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/rooms.css">

<div class="container">
<h2 class="section-title">Available Rooms</h2>
<div class="room-container" style="display:flex; flex-wrap:wrap; gap:20px;">
<?php
$res = $conn->query("SELECT id,room_no,type,description,price,image,status FROM rooms ORDER BY id ASC");
if ($res && $res->num_rows>0):
    while ($r = $res->fetch_assoc()):
        $img = !empty($r['image']) ? $r['image'] : 'images/no-image.jpg';
?>
    <div class="room" style="border:1px solid #ddd; border-radius:6px; padding:12px; width:250px;">
        <img src="/hotel_management_system/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['type']) ?>" style="width:100%; height:150px; object-fit:cover; border-radius:6px;">
        <h3><?= htmlspecialchars($r['type']) ?> (<?= htmlspecialchars($r['room_no']) ?>)</h3>
        <p><?= htmlspecialchars($r['description']) ?></p>
        <p class="price">Price: $<?= number_format($r['price'],2) ?> / night</p>
        <?php if($r['status']=='available'): ?>
            <a class="book-button" href="/hotel_management_system/bookroom.php?room_id=<?= $r['id'] ?>" style="display:inline-block; background:#1abc9c; color:#fff; padding:8px 12px; border-radius:6px; text-decoration:none;">Book Now</a>
        <?php else: ?>
            <span style="color:#888;">Not available</span>
        <?php endif; ?>
    </div>
<?php
    endwhile;
else:
    echo "<p>No rooms found.</p>";
endif;
?>
</div>
</div>
<?php include("includes/footer.php"); ?>
