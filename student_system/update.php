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

// Fetch user data
$query = "SELECT * FROM users WHERE id = '$id'";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $age = intval($_POST['age']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);

    $update_query = "
        UPDATE users SET 
        full_name = '$full_name', 
        address = '$address', 
        age = $age, 
        birthday = '$birthday', 
        gender = '$gender', 
        phone = '$phone', 
        email = '$email' 
        WHERE id = '$id'
    ";

    if ($conn->query($update_query)) {
        echo "User updated successfully.";
        header("Location: admin_dashboard.php"); // Redirect back to admin dashboard
        exit;
    } else {
        echo "Error updating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
</head>
<body>
    <h1>Update User</h1>
    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required><br>
        
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required><br>
        
        <label>Age:</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required><br>
        
        <label>Birthday:</label>
        <input type="date" name="birthday" value="<?php echo htmlspecialchars($user['birthday']); ?>" required><br>
        
        <label>Gender:</label>
        <select name="gender" required>
            <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
        </select><br>
        
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
        
        <button type="submit">Update</button>
    </form>
</body>
</html>
