<?php
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "technova_db"
);

if ($conn->connect_error) {
    die("Database connection failed. Check db.php settings.");
}

$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>