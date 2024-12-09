<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Credentials</title>
    <link rel="stylesheet" href="user_viewpage.css">
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
            background-image: url('image/bucampus.jpg'); /* Replace with your image path */
            background-size: cover; /* Makes the image cover the entire background */
            background-position: center center; /* Centers the image */
            background-attachment: fixed; /* Keeps the background fixed during scroll */
            color: white; /* Adjust text color for readability */
            padding: 20px;
        }

        /* Page header styling */
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: black; /* Light yellow color */
        }

        /* Search form styling */
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        form input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ffeb3b;
            border-radius: 4px;
            font-size: 16px;
            color: black;
        }

        form button {
            padding: 10px 20px;
            background-color: #f44336; /* Red color */
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }

        form button:hover {
            background-color: #d32f2f; /* Darker red */
        }

        /* Table styling */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
            background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
            color: white;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        table th {
            background-color: white; /* Yellow with transparency */
            color: black;
        }

        table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1); /* Lighter row color */
        }

        table td {
            color: white;
        }

        /* Links */
        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: #ffeb3b; /* Light yellow */
            text-decoration: none;
            font-weight: bold;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Search Users</h1>

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
        $query = "SELECT * FROM users WHERE full_name = '$full_name' ORDER BY full_name ASC"; // Removed COLLATE
    } else {
        $query = "SELECT * FROM users ORDER BY full_name ASC"; // Removed COLLATE
    }

    $result = $conn->query($query);

    if (!$result) {
        die("Error in query: " . $conn->error);
    }

    // Display the records
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>full_name</th>
                <th>email</th>
                <th>address</th>
                <th>age</th>
                <th>birthday</th>
                <th>phone</th>
                <th>gender</th>
              </tr>";
        while ($user = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['address']) . "</td>";
            echo "<td>" . htmlspecialchars($user['age']) . "</td>";
            echo "<td>" . htmlspecialchars($user['birthday']) . "</td>";
            echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($user['gender']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No records found.";
    }
    ?>

    <div class="links">
        <a href="homepage.html">Log out</a>
    </div>
</body>
</html>
