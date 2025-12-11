<?php
// Database connection file
// Connect to MySQL database

$conn = new mysqli("localhost", "root", "", "pokemon_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

