# Pokémon Trainer Hub

A complete web application with authentication system featuring Pokémon-themed roles.

## Features

- ✨ Beautiful landing page with Pokémon theme
- 🎮 Pokémon-themed roles (Gym Leader, Elite Four, Champion, Trainer, Researcher)
- 👤 User registration with username and Gmail
- 🔐 Secure login system
- 💾 MySQL database integration
- 📱 Responsive design

## Setup Instructions

### 1. Database Setup

1. Make sure you have MySQL installed and running
2. Open phpMyAdmin or MySQL command line
3. Import the database schema:

```sql
CREATE DATABASE IF NOT EXISTS pokemon_trainer_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pokemon_trainer_hub;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pokemon_role ENUM('gym_leader', 'elite_four', 'champion', 'trainer', 'researcher') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    gmail VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_gmail (gmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Or simply run the `database.sql` file.

### 2. Configuration

Update `config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change if needed
define('DB_PASS', '');              // Change if needed
define('DB_NAME', 'pokemon_trainer_hub');
```

### 3. Server Setup

Make sure you have PHP installed (PHP 7.4 or higher recommended).

**Using XAMPP/WAMP/MAMP:**
1. Copy all files to `htdocs` (XAMPP) or `www` (WAMP/MAMP)
2. Start Apache and MySQL services
3. Open browser and navigate to `http://localhost/Pokemon_project`

**Using PHP Built-in Server:**
```bash
php -S localhost:8000
```
Then open `http://localhost:8000` in your browser

## File Structure

```
Pokemon_project/
├── index.html              # Landing page
├── signup.html             # Sign up page
├── login.html              # Login page
├── dashboard.php           # User dashboard (protected)
├── config.php              # Database configuration
├── signup_process.php      # Sign up handler
├── login_process.php       # Login handler
├── logout.php              # Logout handler
├── database.sql            # Database schema
├── styles.css              # Main stylesheet
└── README.md               # This file
```

## Pokémon Roles

The system supports the following roles:
- 🎒 **Trainer (User)** - Regular Pokémon Trainer
- 🌟 **Champion (Manager)** - Pokémon League Champion
- 👑 **Pokémon Master (Admin)** - Ultimate Pokémon Master

## Security Features

- Password hashing using `password_hash()`
- SQL injection prevention with prepared statements
- Session-based authentication
- Input validation and sanitization
- CSRF protection ready (can be added)

## Testing

1. Go to the signup page
2. Select a Pokémon role
3. Enter a username
4. Enter a Gmail address
5. Create a password
6. Enter the captcha code: **SQUIRTLE**
7. Click "SIGN UP"

Then try logging in with your credentials!

## Notes

- The captcha is currently static (SQUIRTLE). For production, implement a dynamic captcha system.
- Make sure your PHP installation has the `mysqli` extension enabled.
- For production, update `config.php` with secure database credentials and consider using environment variables.

