<?php
require_once '../../database/db_connect.php';

header("Content-Type: application/json");

// 1. Parse Filters
$filters = [
  'status' => $_GET['status'] ?? null,
  'search' => $_GET['search'] ?? null,
  'timeframe' => $_GET['timeframe'] ?? 'today'
];

// 2. Build Secure Query
$query = "SELECT o.order_id, c.first_name, c.last_name, 
                 GROUP_CONCAT(mi.name SEPARATOR ', ') as items,
                 o.status, o.total_amount, o.created_at
          FROM orders o
          JOIN customers c ON o.customer_id = c.customer_id
          JOIN order_items oi ON o.order_id = oi.order_id
          JOIN menu_items mi ON oi.item_id = mi.item_id";

$conditions = [];
$params = [];

if ($filters['status']) {
  $conditions[] = "o.status = ?";
  $params[] = $filters['status'];
}

if ($filters['search']) {
  $conditions[] = "(c.first_name LIKE ? OR c.last_name LIKE ?)";
  $params[] = "%{$filters['search']}%";
  $params[] = "%{$filters['search']}%";
}

// Add timeframe condition
// ...

// 3. Execute Query
$stmt = $pdo->prepare($query . (count($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : ''));
$stmt->execute($params);

// 4. Format Response
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($orders);
?>