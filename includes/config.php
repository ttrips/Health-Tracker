<?php
// ============================================================
//  Database Configuration
//  Copy this file to config.php and update your credentials
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // your MySQL username
define('DB_PASS', 'abhideep');            // your MySQL password
define('DB_NAME', 'health_tracker');
define('APP_NAME', 'Health Tracker');
define('APP_URL', 'http://localhost/health-tracker|http://localhost:8000');

// Session lifetime (seconds)
define('SESSION_LIFETIME', 3600);

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}
