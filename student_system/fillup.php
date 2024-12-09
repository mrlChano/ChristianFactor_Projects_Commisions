<?php
include_once "db.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $age = (int)$_POST['age'];
    $birthday = $_POST['birthday'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $phone = $conn->real_escape_string($_POST['phone']);  // New phone field

    // Check if the full name already exists in the database
    $check_query = "SELECT * FROM users WHERE full_name = '$full_name'";
    $check_result = $conn->query($check_query);

    if ($check_result && $check_result->num_rows > 0) {
        // Full name already exists, display an alert and stop the registration
        echo "<script>
                alert('Full Name already exists. Please try another one.');
                window.location.href = 'fillup.php'; // Redirect to fill-up page to try again
              </script>";
    } else {
        // Full name is unique, proceed to insert data into the database
        $sql = "INSERT INTO users (full_name, email, address, age, birthday, gender, phone) 
                VALUES ('$full_name', '$email', '$address', $age, '$birthday', '$gender', '$phone')";

        if ($conn->query($sql) === TRUE) {
            // Success message and redirection
            $_SESSION['full_name'] = $full_name;  // Store the full name in session to be used later
            echo "<script>
                    alert('Registration successful!');
                    window.location.href = 'user_viewpage.php?search=" . urlencode($full_name) . "'; 
                  </script>";
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill-Up Form</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Fill-up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="field input">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" required>
                </div>
                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" required>
                </div>

                <div class="field input">
                    <label for="birthday">Birthday</label>
                    <input type="text" name="birthday" id="birthday" required placeholder="MM/DD/YYYY">
                </div>

                <div class="field input">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="field input">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" pattern="^09\d{9}$" placeholder="09123456789" required>
                    <small>Enter phone number in the format 09123456789</small>
                </div>

                <input type="submit" value="Register">
            </form>
            <div class="links">
                <a href="signup.php">Log out</a>
            </div>
        </div>
    </div>

    <script>
        // Birthday field format handler
        document.getElementById('birthday').addEventListener('input', function(e) {
            let date = e.target.value;
            if (date.length === 2 || date.length === 5) {
                e.target.value += '/';
            }
        });
    </script>
</body>
</html>
