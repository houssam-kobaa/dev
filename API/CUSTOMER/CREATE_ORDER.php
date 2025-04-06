<?php
require_once '../../database/db_connect.php';
require_once '../../config.php';

header("Content-Type: application/json");

// 1. Input Validation
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data['customer']['name']) {
  http_response_code(400);
  die(json_encode(['error' => 'Name is required']));
}

// 2. Database Transaction
try {
  $pdo->beginTransaction();

  // Insert customer
  $stmt = $pdo->prepare("INSERT INTO customers (...) VALUES (...)");
  $stmt->execute([...]);
  $customerId = $pdo->lastInsertId();

  // Insert order
  $stmt = $pdo->prepare("INSERT INTO orders (...) VALUES (...)");
  $stmt->execute([...]);
  $orderId = $pdo->lastInsertId();

  // Insert order items
  foreach ($data['items'] as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (...) VALUES (...)");
    $stmt->execute([...]);
  }

  $pdo->commit();
  echo json_encode(['order_id' => $orderId]);

} catch (PDOException $e) {
  $pdo->rollBack();
  http_response_code(500);
  die(json_encode(['error' => 'Database error']));
}
?>