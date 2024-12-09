<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        /* Basic resets */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: Arial, sans-serif;
            background-image: url('image/bucampus.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            color: black;
        }

        /* Page header */
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Search form */
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        form input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid orange;
            border-radius: 4px;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            background-color: #4caf50;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        form button:hover {
            background-color: #45a049;
        }

        /* Table styling */
        table {
            width: 90%;
            max-width: 1200px;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.9);
            color: black;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ccc;
        }

        table th {
            background-color: rgba(0, 0, 0, 0.2);
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Buttons in the table */
        .button {
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
        }

        .update-button {
            background-color: #2196f3;
            color: white;
        }

        .update-button:hover {
            background-color: #0b7dda;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .delete-button:hover {
            background-color: #d32f2f;
        }

        /* No records message */
        .no-records {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #ff5722;
        }

        /* Footer links */
        .links {
            margin-top: 20px;
        }

        .links a {
            color: red;
            text-decoration: none;
            font-weight: bold;
            
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Manage Users</h1>

    <!-- Search Form -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Enter name to search" 
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Search</button>
    </form>

    <?php
    include_once "db.php";
    session_start();

    // Set the connection character set to utf8mb4
    $conn->set_charset('utf8mb4');

    // Fetch all users or search for a specific user
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $full_name = $conn->real_escape_string($_GET['search']);
        $query = "SELECT * FROM users WHERE full_name LIKE '%$full_name%' ORDER BY full_name ASC";
    } else {
        $query = "SELECT * FROM users ORDER BY full_name ASC";
    }

    $result = $conn->query($query);

    if (!$result) {
        die("Error in query: " . $conn->error);
    }

    // Display the records
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Age</th>
                <th>Birthday</th>
                <th>Gender</th>
                <th>Actions</th>
              </tr>";
        while ($user = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['address']) . "</td>";
            echo "<td>" . htmlspecialchars($user['age']) . "</td>";
            echo "<td>" . htmlspecialchars($user['birthday']) . "</td>";
            echo "<td>" . htmlspecialchars($user['gender']) . "</td>";
            echo "<td>
                    <a href='update.php?id=" . $user['id'] . "' class='button update-button'>Update</a>
                    <a href='delete.php?id=" . $user['id'] . "' class='button delete-button' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-records'>No records found.</p>";
    }
    ?>

    <div class="links">
        <a href="homepage.html">Log out</a>
    </div>
</body>
</html>
