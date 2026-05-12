<?php
// controllers/CustomerOrderController.php

require_once '../config/database.php';
require_once '../models/Order.php';
require_once '../models/OrderItem.php';
require_once '../models/Review.php';
require_once '../config/helpers.php';

function customer_orders_action() {
    global $pdo;
    require_login();
    
    $user_id = $_SESSION['user_id'];
    $orders = get_orders_by_user($pdo, $user_id);
    
    // Fetch items for each order for the accordion
    foreach ($orders as &$order) {
        $order['items'] = get_order_items($pdo, $order['id']);
        foreach ($order['items'] as &$item) {
            $item['has_reviewed'] = has_user_reviewed($pdo, $item['product_id'], $user_id);
        }
    }
    
    $content_view = '../views/customer/orders.php';
    require '../views/layout.php';
}

?>
