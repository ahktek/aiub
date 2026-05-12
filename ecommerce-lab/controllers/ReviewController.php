<?php
// controllers/ReviewController.php

require_once '../config/database.php';
require_once '../models/Review.php';
require_once '../models/Order.php';

function api_leave_review() {
    global $pdo;
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['product_id']) && isset($input['rating'])) {
            $product_id = intval($input['product_id']);
            $rating = intval($input['rating']);
            $review_text = isset($input['review_text']) ? trim($input['review_text']) : '';
            $user_id = $_SESSION['user_id'];
            
            if ($rating < 1 || $rating > 5) {
                echo json_encode(['error' => 'Rating must be between 1 and 5']);
                exit;
            }

            // Verify the user has a "Delivered" order for this product
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.user_id = ? AND o.status = 'Delivered' AND oi.product_id = ?
            ");
            $stmt->execute([$user_id, $product_id]);
            if ($stmt->fetchColumn() == 0) {
                echo json_encode(['error' => 'You can only review products you have purchased and received.']);
                exit;
            }

            if (create_review($pdo, $product_id, $user_id, $rating, $review_text)) {
                echo json_encode(['ok' => true]);
            } else {
                echo json_encode(['error' => 'You have already reviewed this product.']);
            }
            exit;
        }
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}

function api_get_reviews() {
    global $pdo;
    header('Content-Type: application/json');
    
    if (isset($_GET['id'])) {
        $product_id = intval($_GET['id']);
        $reviews = get_product_reviews($pdo, $product_id);
        
        // Format dates
        foreach ($reviews as &$r) {
            $r['created_at'] = date('M d, Y', strtotime($r['created_at']));
        }
        
        echo json_encode(['ok' => true, 'reviews' => $reviews]);
        exit;
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}
?>
