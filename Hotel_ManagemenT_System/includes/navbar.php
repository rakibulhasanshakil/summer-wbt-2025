<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$usertype = isset($_SESSION['usertype']) ? $_SESSION['usertype'] : null;
?>
<nav class="nav-main">
<div class="nav-links">
<a href="../index.php">Home</a>
<a href="../rooms.php">Rooms</a>
<a href="../bookroom.php">Book a Room</a>
<?php if (!$usertype): ?>
    <a href="../signin.php">Sign In</a>
    <a href="../signup.php">Sign Up</a>
<?php else: ?>
    <a href="../profile.php">Profile</a>
    <a href="../signout.php">Sign Out</a>
<?php endif; ?>
</div>
</nav>
