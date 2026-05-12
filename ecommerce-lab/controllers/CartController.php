<?php
// controllers/CartController.php

require_once '../config/database.php';
require_once '../models/Product.php';
require_once '../config/helpers.php';

function init_cart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cart_view_action() {
    global $pdo;
    require_login();
    init_cart();

    $cart_items = [];
    $grand_total = 0;

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = get_product_by_id($pdo, $product_id);
        if ($product) {
            $line_total = $product['price'] * $quantity;
            $grand_total += $line_total;
            
            $cart_items[] = [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'line_total' => $line_total,
                'stock_qty' => $product['stock_qty']
            ];
        } else {
            // Product might have been deleted, remove from cart
            unset($_SESSION['cart'][$product_id]);
        }
    }

    $content_view = '../views/shop/cart.php';
    require '../views/layout.php';
}

function get_cart_count() {
    init_cart();
    $count = 0;
    foreach ($_SESSION['cart'] as $qty) {
        $count += $qty;
    }
    return $count;
}

function api_cart_add() {
    global $pdo;
    header('Content-Type: application/json');
    require_login(); // Assuming AJAX request handles redirect or we just check session
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['product_id'])) {
            $product_id = intval($input['product_id']);
            $product = get_product_by_id($pdo, $product_id);
            
            if ($product && $product['is_available'] && $product['stock_qty'] > 0) {
                init_cart();
                
                $current_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
                $new_qty = $current_qty + 1;
                
                if ($new_qty <= $product['stock_qty']) {
                    $_SESSION['cart'][$product_id] = $new_qty;
                    echo json_encode(['ok' => true, 'count' => get_cart_count()]);
                } else {
                    echo json_encode(['error' => 'Not enough stock available']);
                }
                exit;
            } else {
                echo json_encode(['error' => 'Product unavailable']);
                exit;
            }
        }
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}

function api_cart_update() {
    global $pdo;
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['product_id']) && isset($input['action'])) {
            $product_id = intval($input['product_id']);
            $action = $input['action']; // 'inc' or 'dec'
            
            init_cart();
            if (isset($_SESSION['cart'][$product_id])) {
                $product = get_product_by_id($pdo, $product_id);
                $current_qty = $_SESSION['cart'][$product_id];
                
                if ($action === 'inc') {
                    if ($current_qty + 1 <= $product['stock_qty']) {
                        $_SESSION['cart'][$product_id]++;
                    } else {
                        echo json_encode(['error' => 'Max stock reached']);
                        exit;
                    }
                } elseif ($action === 'dec') {
                    if ($current_qty - 1 > 0) {
                        $_SESSION['cart'][$product_id]--;
                    } else {
                        unset($_SESSION['cart'][$product_id]);
                    }
                }
                
                // Recalculate totals
                $new_qty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;
                $line_total = $new_qty * $product['price'];
                
                $grand_total = 0;
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    $p = get_product_by_id($pdo, $pid);
                    $grand_total += $p['price'] * $qty;
                }
                
                echo json_encode([
                    'ok' => true, 
                    'new_qty' => $new_qty,
                    'line_total' => $line_total,
                    'grand_total' => $grand_total,
                    'count' => get_cart_count()
                ]);
                exit;
            }
        }
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}

function api_cart_remove() {
    global $pdo;
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['product_id'])) {
            $product_id = intval($input['product_id']);
            init_cart();
            
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
            }
            
            // Recalculate grand total
            $grand_total = 0;
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $p = get_product_by_id($pdo, $pid);
                $grand_total += $p['price'] * $qty;
            }
            
            echo json_encode([
                'ok' => true, 
                'grand_total' => $grand_total,
                'count' => get_cart_count()
            ]);
            exit;
        }
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}
?>
