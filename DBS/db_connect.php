<?php
$host = 'localhost';
$db   = 'pizza_delivery';
$user = 'app_user';
$pass = 'secure_password_123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die(json_encode(['error' => 'Database connection failed']));
}
?>