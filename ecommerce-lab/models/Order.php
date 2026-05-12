<?php
// models/Order.php

function create_order($pdo, $user_id, $shipping_address, $payment_method, $total_amount) {
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, shipping_address, payment_method, total_amount)
        VALUES (?, ?, ?, ?)
    ");
    if ($stmt->execute([$user_id, $shipping_address, $payment_method, $total_amount])) {
        return $pdo->lastInsertId();
    }
    return false;
}

function get_orders_by_user($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function get_all_orders($pdo) {
    // For admin
    $stmt = $pdo->query("
        SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.id DESC
    ");
    return $stmt->fetchAll();
}

function get_order_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function update_order_status($pdo, $id, $status) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}
?>
