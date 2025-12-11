-- Create database
CREATE DATABASE IF NOT EXISTS pokemon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pokemon_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pokemon_role ENUM('trainer', 'champion', 'pokemon_master') NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    gmail VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_gmail (gmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

