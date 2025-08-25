<?php
// Database configuration
$host = "localhost";       // MySQL host
$user = "root";            // MySQL username (default XAMPP)
$pass = "";                // MySQL password (default XAMPP is empty)
$dbname = "hotel_database";      // The database you created

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to avoid issues with special characters
$conn->set_charset("utf8");

?>
