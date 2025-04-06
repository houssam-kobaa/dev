<?php
require_once '../../database/db_connect.php';
require_once '../../config.php';

header("Content-Type: application/json");

// 1. Admin Authentication
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

// 2. Validate Input
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['order_id']) || !isset($data['new_status'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing parameters']));
}

// 3. Validate Status Transition
$allowedStatuses = [
    'received' => ['preparing', 'cancelled'],
    'preparing' => ['baking', 'cancelled'],
    'baking' => ['out_for_delivery'],
    'out_for_delivery' => ['delivered']
];

try {
    // Check current status
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE order_id = ?");
    $stmt->execute([$data['order_id']]);
    $current = $stmt->fetchColumn();
    
    if (!in_array($data['new_status'], $allowedStatuses[$current] ?? [])) {
        http_response_code(400);
        die(json_encode(['error' => 'Invalid status transition']));
    }
    
    // Update status
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$data['new_status'], $data['order_id']]);
    
    // If delivered, set delivery time
    if ($data['new_status'] === 'delivered') {
        $pdo->prepare("UPDATE orders SET delivered_at = NOW() WHERE order_id = ?")
           ->execute([$data['order_id']]);
    }
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error']));
}
?>