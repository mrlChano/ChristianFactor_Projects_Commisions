<?php
// Database configuration
$servername = "localhost"; // Host name
$username = "root";        // Default MySQL username
$password = "123456";      // Your MySQL password
$dbname = "studentsytem";  // New database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to match your database settings
$conn->set_charset("utf8mb4");
?>
