<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel_db";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Helper: format date from YYYY-MM-DD to dd/mm/yyyy for display
function fmtDateDisplay($date) {
    if (!$date || $date === '0000-00-00') return '';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d) return $date;
    return $d->format('d/m/Y');
}

// Helper: convert dd/mm/yyyy to YYYY-MM-DD (for storage)
function parseDateInput($d) {
    $d = trim($d);
    if (!$d) return null;
    // If already yyyy-mm-dd input (from input[type=date]) then accept
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return $d;
    // If dd/mm/yyyy
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $d, $m)) {
        return "{$m[3]}-{$m[2]}-{$m[1]}";
    }
    // fallback try strtotime
    $ts = strtotime($d);
    if ($ts) return date('d-m-y', $ts);
    return null;
}
?>
