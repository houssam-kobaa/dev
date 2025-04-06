<?php
require_once '../../database/db_connect.php';
require_once '../../config.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents('php://input'), true);

// 1. Validate Input
if (empty($data['username']) || empty($data['password'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing credentials']));
}

// 2. Secure Authentication
$stmt = $pdo->prepare("
    SELECT admin_id, password_hash 
    FROM admin_users 
    WHERE username = ? 
    AND is_active = 1
    LIMIT 1
");
$stmt->execute([$data['username']]);
$admin = $stmt->fetch();

// 3. Verify Password
if (!$admin || !password_verify($data['password'], $admin['password_hash'])) {
    sleep(2); // Delay to prevent brute force
    http_response_code(401);
    die(json_encode(['error' => 'Invalid credentials']));
}

// 4. Create Session
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = $admin['admin_id'];

echo json_encode(['success' => true]);
?>