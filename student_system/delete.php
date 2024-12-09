<?php
include_once "db.php";
session_start();

// Set the connection character set to utf8mb4
$conn->set_charset('utf8mb4');

// Check if `id` is provided
if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

$id = $conn->real_escape_string($_GET['id']);

// Delete user from database
$query = "DELETE FROM users WHERE id = '$id'";

if ($conn->query($query)) {
    echo "User deleted successfully.";
    header("Location: admin_dashboard.php"); // Redirect back to admin dashboard
    exit;
} else {
    echo "Error deleting user: " . $conn->error;
}
?>
