<?php
require_once '../../database/db_connect.php';
require_once '../../config.php';

header("Content-Type: application/json");

// 1. Authentication Check
if (!isset($_SERVER['PHP_AUTH_USER']) || 
    $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME || 
    $_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
    header('WWW-Authenticate: Basic realm="Admin Access"');
    http_response_code(401);
    die(json_encode(['error' => 'Authentication required']));
}

// 2. Input Validation
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['order_id']) || !is_numeric($data['order_id'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid order ID']));
}

try {
    // 3. Soft Delete Implementation (safer than hard delete)
    $stmt = $pdo->prepare("UPDATE orders SET is_deleted = 1 WHERE order_id = ?");
    $stmt->execute([$data['order_id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        die(json_encode(['error' => 'Order not found']));
    }

    // 4. Log the deletion
    error_log("Order #{$data['order_id']} deleted by admin");

    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
}
?>