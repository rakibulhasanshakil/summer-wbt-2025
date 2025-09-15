<?php
session_start();
session_unset();
session_destroy();
header("Location: /hotel_management_system/index.php");
exit;
