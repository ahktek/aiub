<?php
// models/Product.php

function get_all_products_admin($pdo) {
    // Fetch products with category name and average rating
    // COALESCE handles cases where there are no reviews yet
    $stmt = $pdo->query("
        SELECT 
            p.*, 
            c.name as category_name,
            (SELECT COALESCE(AVG(rating), 0) FROM reviews r WHERE r.product_id = p.id) as avg_rating
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC
    ");
    return $stmt->fetchAll();
}

function get_product_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function create_product($pdo, $category_id, $name, $description, $price, $stock_qty, $primary_image_path) {
    $stmt = $pdo->prepare("
        INSERT INTO products (category_id, name, description, price, stock_qty, primary_image_path) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$category_id, $name, $description, $price, $stock_qty, $primary_image_path]);
}

function update_product($pdo, $id, $category_id, $name, $description, $price, $stock_qty, $primary_image_path) {
    $query = "UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock_qty = ?";
    $params = [$category_id, $name, $description, $price, $stock_qty];
    
    if ($primary_image_path !== null) {
        $query .= ", primary_image_path = ?";
        $params[] = $primary_image_path;
    }
    
    $query .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}

function delete_product($pdo, $id) {
    // Check if product is in any orders
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        return false;
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    return $stmt->execute([$id]);
}

function update_product_availability($pdo, $id, $is_available) {
    $stmt = $pdo->prepare("UPDATE products SET is_available = ? WHERE id = ?");
    return $stmt->execute([$is_available, $id]);
}
?>
