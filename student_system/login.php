<?php
// Include the database connection
include_once "db.php";  // Ensure this file connects to your database

// Start the session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input and sanitize
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query users table to check credentials
    $sql = "SELECT id, Password FROM users WHERE Username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username); // Bind username parameter
        $stmt->execute();
        $stmt->store_result();
        
        // Check if the username exists
        if ($stmt->num_rows > 0) {
            // Bind only the columns that are returned from the query: id and Password
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Password is correct, now check if the user has filled in details in the users table
                $sql = "SELECT full_name, email, address, age, gender FROM users WHERE id = ?";
                if ($stmt2 = $conn->prepare($sql)) {
                    $stmt2->bind_param("i", $id);
                    $stmt2->execute();
                    $stmt2->store_result();
                    
                    // Fetch the details if available
                    if ($stmt2->num_rows > 0) {
                        $stmt2->bind_result($full_name, $email, $address, $age, $gender);
                        $stmt2->fetch();
                        
                        // Store user data in session
                        $_SESSION['id'] = $id;
                        $_SESSION['full_name'] = $full_name;
                        $_SESSION['email'] = $email;
                        $_SESSION['address'] = $address;
                        $_SESSION['age'] = $age;
                        $_SESSION['gender'] = $gender;

                        // Redirect to user view page
                        header("Location: user_viewpage.php");
                        exit; // Make sure the script stops after the redirect
                    } else {
                        // User hasn't filled up their details, redirect to the fill-up page
                        header("Location: fillup.php");
                        exit; // Stop the script
                    }
                } else {
                    // If there's an error with the second query
                    echo "<script>alert('Error checking user details.');</script>";
                }
            } else {
                // Incorrect password, show prompt
                echo "<script>alert('Invalid username or password.');</script>";
            }
        } else {
            // If username is not found, show prompt
            echo "<script>alert('Invalid username or password.');</script>";
        }
        $stmt->close();  // Close the first statement
    } else {
        // If the first query fails, show prompt
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
    <title>Login Form</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username"><i class="fas fa-user"></i> Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password"><i class="fas fa-key"></i> Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
        <div class="links">
            Don't have an account? <a href="signup.php">Sign Up Now</a>
        </div>
    </div>
</body>
</html>
