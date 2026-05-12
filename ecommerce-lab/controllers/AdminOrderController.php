<?php
// controllers/AdminOrderController.php

require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../config/helpers.php';

function admin_orders_action() {
    global $pdo;
    require_admin();

    // Default dates
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';

    $query = "
        SELECT o.*, u.name as customer_name, u.email as customer_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE DATE(o.created_at) >= ? AND DATE(o.created_at) <= ?
    ";
    $params = [$start_date, $end_date];

    if (!empty($status_filter)) {
        $query .= " AND o.status = ?";
        $params[] = $status_filter;
    }

    $query .= " ORDER BY o.id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

    $content_view = '../views/admin/orders.php';
    require '../views/layout.php';
}

function api_update_order_status() {
    global $pdo;
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($_GET['id']) && isset($input['status'])) {
            $id = $_GET['id'];
            $status = $input['status'];
            
            $allowed_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
            if (!in_array($status, $allowed_statuses)) {
                echo json_encode(['error' => 'Invalid status value']);
                exit;
            }
            
            if (update_order_status($pdo, $id, $status)) {
                echo json_encode(['ok' => true]);
                exit;
            } else {
                echo json_encode(['error' => 'Failed to update status']);
                exit;
            }
        }
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}
?>
