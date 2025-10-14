<?php
require_once "includes/db.php";
include("includes/header.php");
?>
<link rel="stylesheet" href="./css/rooms.css">
<script defer src="./js/rooms.js"></script>

<!-- Hero Section -->
<section class="room-hero">
    <div class="hero-content">
        <h1>Our Luxury Rooms</h1>
        <p>Experience unparalleled comfort and elegance</p>
    </div>
</section>

<!-- Room Filter Section -->
<section class="room-filter">
    <div class="container">
        <div class="filter-container">
            <div class="filter-group">
                <label>Room Type</label>
                <select id="roomType">
                    <option value="">All Types</option>
                    <option value="Standard">Standard</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Price Range</label>
                <select id="priceRange">
                    <option value="">All Prices</option>
                    <option value="0-100">$0 - $100</option>
                    <option value="101-200">$101 - $200</option>
                    <option value="201+">$201+</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Availability</label>
                <select id="availability">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="booked">Booked</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section class="rooms-section">
    <div class="container">
        <div class="section-header">
            <h2>Available Accommodations</h2>
            <p>Select from our range of luxurious rooms and suites</p>
        </div>
        
        <div class="room-grid">
            <?php
            $res = $conn->query("SELECT id, room_no, type, description, price, image, status FROM rooms ORDER BY price ASC");
            if ($res && $res->num_rows > 0):
                while ($room = $res->fetch_assoc()):
                    $img = !empty($room['image']) ? $room['image'] : 'images/no-image.jpg';
                    $amenities = [
                        'Standard' => ['<i class="fas fa-wifi"></i> Free WiFi', '<i class="fas fa-tv"></i> Smart TV', '<i class="fas fa-snowflake"></i> AC'],
                        'Deluxe' => ['<i class="fas fa-wifi"></i> Free WiFi', '<i class="fas fa-tv"></i> Smart TV', '<i class="fas fa-snowflake"></i> AC', '<i class="fas fa-coffee"></i> Mini Bar'],
                        'Suite' => ['<i class="fas fa-wifi"></i> Free WiFi', '<i class="fas fa-tv"></i> Smart TV', '<i class="fas fa-snowflake"></i> AC', '<i class="fas fa-coffee"></i> Mini Bar', '<i class="fas fa-couch"></i> Living Area']
                    ];
                    $roomAmenities = $amenities[$room['type']] ?? $amenities['Standard'];
            ?>
            <div class="room-card" data-type="<?= htmlspecialchars($room['type']) ?>" data-price="<?= $room['price'] ?>">
                <div class="room-image">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($room['type']) ?>">
                    <?php if($room['status'] == 'available'): ?>
                        <div class="room-tag available">Available</div>
                    <?php else: ?>
                        <div class="room-tag booked">Booked</div>
                    <?php endif; ?>
                </div>
                <div class="room-details">
                    <div class="room-type"><?= htmlspecialchars($room['type']) ?></div>
                    <h3><?= htmlspecialchars($room['type']) ?> Room <?= htmlspecialchars($room['room_no']) ?></h3>
                    <p class="room-description"><?= htmlspecialchars($room['description']) ?></p>
                    <div class="room-amenities">
                        <?php foreach($roomAmenities as $amenity): ?>
                            <span><?= $amenity ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="room-footer">
                        <div class="room-price">
                            <span class="price">$<?= number_format($room['price'], 2) ?></span>
                            <span class="per-night">per night</span>
                        </div>
                        <?php if($room['status'] == 'available'): ?>
                            <a href="bookroom.php?room_id=<?= $room['id'] ?>" class="btn-book">
                                Book Now <i class="fas fa-arrow-right"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn-book disabled" disabled>Not Available</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <div class="no-rooms">
                <i class="fas fa-bed"></i>
                <h3>No Rooms Available</h3>
                <p>Please check back later for availability</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
