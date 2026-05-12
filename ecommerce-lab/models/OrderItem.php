<?php
// models/OrderItem.php

function create_order_item($pdo, $order_id, $product_id, $quantity, $unit_price) {
    $stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, unit_price)
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$order_id, $product_id, $quantity, $unit_price]);
}

function get_order_items($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.primary_image_path 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}
?>
