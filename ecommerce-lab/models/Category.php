<?php
// models/Category.php

function get_all_categories($pdo) {
    // Fetch all categories and join with parent to get parent name
    $stmt = $pdo->query("
        SELECT c.*, p.name as parent_name 
        FROM categories c 
        LEFT JOIN categories p ON c.parent_id = p.id
        ORDER BY c.name ASC
    ");
    return $stmt->fetchAll();
}

function create_category($pdo, $name, $parent_id) {
    if (empty($parent_id)) {
        $parent_id = null;
    }
    $stmt = $pdo->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
    return $stmt->execute([$name, $parent_id]);
}

function get_category_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function update_category($pdo, $id, $name, $parent_id) {
    if (empty($parent_id)) {
        $parent_id = null;
    }
    // Prevent setting a category as its own parent
    if ($id == $parent_id) {
        return false;
    }
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, parent_id = ? WHERE id = ?");
    return $stmt->execute([$name, $parent_id, $id]);
}

function delete_category($pdo, $id) {
    // Check for child categories
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE parent_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        return false; // Cannot delete, has children
    }

    // Check for products in category
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        return false; // Cannot delete, has products
    }

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}
?>
