<?php
require_once "includes/db.php";
include("includes/header.php");
include("includes/navbar.php");
?>
<link rel="stylesheet" href="/hotel_management_system/css/rooms.css">
<div class="container">
  <h2 class="section-title">Available Rooms</h2>
  <div class="room-container">
    <?php
    $res = $conn->query("SELECT id,room_no,type,description,price,image,status FROM rooms ORDER BY id ASC");
    if ($res && $res->num_rows>0) {
        while ($r = $res->fetch_assoc()):
            $img = !empty($r['image']) ? $r['image'] : 'images/no-image.jpg';
    ?>
      <div class="room">
        <img src="/hotel_management_system/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($r['type']); ?>">
        <h3><?php echo htmlspecialchars($r['type']) . " (" . htmlspecialchars($r['room_no']) . ")"; ?></h3>
        <p><?php echo htmlspecialchars($r['description']); ?></p>
        <p class="price">Price: $<?php echo number_format($r['price'],2); ?> / night</p>
        <?php if ($r['status']=='available'): ?>
          <a class="book-button" href="/hotel_management_system/bookroom.php?room_id=<?php echo $r['id']; ?>">Book Now</a>
        <?php else: ?>
          <span style="color:#888;">Not available</span>
        <?php endif; ?>
      </div>
    <?php
        endwhile;
    } else {
        echo "<p>No rooms found.</p>";
    }
    ?>
  </div>
</div>
<?php include("includes/footer.php"); ?>
