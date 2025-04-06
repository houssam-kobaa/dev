<?php
require_once '../../database/db_connect.php';

header("Content-Type: application/json");
header("Cache-Control: max-age=3600"); // Cache for 1 hour

try {
    // 1. Get all available menu items
    $stmt = $pdo->query("
        SELECT item_id, name, description, category, size, price 
        FROM menu_items 
        WHERE is_available = 1
        ORDER BY category, price
    ");
    
    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Group by category for easier frontend display
    $categorized = [];
    foreach ($menu as $item) {
        $categorized[$item['category']][] = $item;
    }
    
    // 3. Add metadata
    $response = [
        'last_updated' => time(),
        'menu' => $categorized
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Failed to load menu']));
}
?>