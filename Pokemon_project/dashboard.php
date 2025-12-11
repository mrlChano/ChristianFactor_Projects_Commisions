<?php
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$username = $_SESSION['username'];
$pokemon_role = $_SESSION['pokemon_role'];

// Role display names
$role_names = [
    'trainer' => '🎒 Trainer (User)',
    'champion' => '🌟 Champion (Manager)',
    'pokemon_master' => '👑 Pokémon Master (Admin)'
];

$role_display = $role_names[$pokemon_role] ?? $pokemon_role;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pokémon Trainer Hub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }
        .welcome-card {
            background: #fff;
            border: 3px solid #2196f3;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .welcome-title {
            font-size: 2.5rem;
            color: #1976d2;
            margin-bottom: 15px;
        }
        .user-info {
            font-size: 1.2rem;
            color: #666;
            margin: 10px 0;
        }
        .role-badge {
            display: inline-block;
            background: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #f44336;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body class="landing-page">
    <div class="background-elements">
        <div class="circle circle-yellow circle-1"></div>
        <div class="circle circle-blue circle-2"></div>
        <div class="pokeball-bg"></div>
    </div>

    <div class="dashboard-container">
        <div class="welcome-card">
            <h1 class="welcome-title">Welcome, <?php echo htmlspecialchars($username); ?>! 🎉</h1>
            <div class="user-info">
                <p>Your Role: <span class="role-badge"><?php echo htmlspecialchars($role_display); ?></span></p>
            </div>
            <a href="logout.php" class="logout-btn">Log Out</a>
        </div>
    </div>

    <footer class="footer">
        <p>© 2025 Pokémon Trainer Hub. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <span> • </span>
            <a href="#">Terms of Service</a>
            <span> • </span>
            <a href="#">Powered by Readdy</a>
        </div>
    </footer>
</body>
</html>

