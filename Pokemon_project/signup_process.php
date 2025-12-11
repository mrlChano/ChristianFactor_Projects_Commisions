<?php
include 'db.php';

header('Content-Type: application/json');

// Throw mysqli errors as exceptions so we can respond with JSON
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get form data
    $pokemon_role = isset($_POST['pokemon_role']) ? trim($_POST['pokemon_role']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $gmail = isset($_POST['gmail']) ? trim($_POST['gmail']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $captcha = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';

    // Validate inputs
    $errors = [];

    // Validate Pokémon Role
    $valid_roles = ['trainer', 'champion', 'pokemon_master'];
    if (empty($pokemon_role) || !in_array($pokemon_role, $valid_roles)) {
        $errors[] = 'Please select a valid Pokémon role';
    }

    // Validate Username
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }

    // Validate Gmail
    if (empty($gmail)) {
        $errors[] = 'Gmail is required';
    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    // Validate Password
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }

    // Validate Confirm Password
    if (empty($confirm_password)) {
        $errors[] = 'Please confirm your password';
    } elseif ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Validate Captcha (case-insensitive) using the session-stored value
    $sessionCaptcha = isset($_SESSION['captcha_code']) ? $_SESSION['captcha_code'] : '';
    if (empty($captcha)) {
        $errors[] = 'Please enter the Pokémon security code';
    } elseif (empty($sessionCaptcha)) {
        $errors[] = 'Verification code missing or expired. Please refresh the code and try again.';
    } elseif (strcasecmp($captcha, $sessionCaptcha) !== 0) {
        $errors[] = 'Invalid Pokémon security code';
    }

    // If there are errors, return them
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
        exit;
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }
    $stmt->close();

    // Check if gmail already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE gmail = ?");
    $stmt->bind_param("s", $gmail);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (pokemon_role, username, gmail, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $pokemon_role, $username, $gmail, $hashed_password);

    if ($stmt->execute()) {
        // Set session variables
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        $_SESSION['pokemon_role'] = $pokemon_role;

        $stmt->close();
        $conn->close();

        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! Redirecting to login...',
            'redirect' => 'login.html'
        ]);
    } else {
        $stmt->close();
        $conn->close();
        echo json_encode(['success' => false, 'message' => 'Error creating account. Please try again.']);
    }
} catch (Throwable $e) {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }

    // Return a clear error message to the frontend
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

