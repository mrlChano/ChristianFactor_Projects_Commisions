<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$login_identifier = isset($_POST['login_identifier']) ? trim($_POST['login_identifier']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate inputs
if (empty($login_identifier)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your email or username']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your password']);
    exit;
}

// Check if login_identifier is email or username
$is_email = filter_var($login_identifier, FILTER_VALIDATE_EMAIL);

if ($is_email) {
    // Login by email
    $stmt = $conn->prepare("SELECT id, username, pokemon_role, password FROM users WHERE gmail = ?");
} else {
    // Login by username
    $stmt = $conn->prepare("SELECT id, username, pokemon_role, password FROM users WHERE username = ?");
}

$stmt->bind_param("s", $login_identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Invalid email/username or password']);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Invalid email/username or password']);
    exit;
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['pokemon_role'] = $user['pokemon_role'];

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true, 
    'message' => 'Login successful! Redirecting...',
    'redirect' => 'dashboard.php'
]);
?>

