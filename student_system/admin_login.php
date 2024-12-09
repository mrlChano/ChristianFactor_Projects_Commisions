<?php
// Include the database connection
include_once "db.php"; // Ensure this file connects to your database

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input and sanitize
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query admin table to check credentials
    $sql = "SELECT id, Password FROM users WHERE Username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Check if the username exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Successful login, store admin data in session
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;

                // Redirect to admin dashboard
                header("Location: admin_dashboard.php");
                exit;
            } else {
                // Incorrect password
                echo "<script>alert('Invalid username or password.');</script>";
            }
        } else {
            // Username not found
            echo "<script>alert('Invalid username or password.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing query.');</script>";
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form action="admin_login.php" method="POST">
            <label for="username"><i class="fas fa-user"></i> Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password"><i class="fas fa-key"></i> Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
