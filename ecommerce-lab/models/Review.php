<?php
// models/Review.php

function create_review($pdo, $product_id, $user_id, $rating, $review_text) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (product_id, user_id, rating, review_text)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$product_id, $user_id, $rating, $review_text]);
    } catch (\PDOException $e) {
        // Catch duplicate entry error due to UNIQUE constraint
        if ($e->getCode() == 23000) {
            return false;
        }
        throw $e;
    }
}

function get_product_reviews($pdo, $product_id) {
    $stmt = $pdo->prepare("
        SELECT r.*, u.name as customer_name 
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.product_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

function has_user_reviewed($pdo, $product_id, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE product_id = ? AND user_id = ?");
    $stmt->execute([$product_id, $user_id]);
    return $stmt->fetchColumn() > 0;
}
?>
