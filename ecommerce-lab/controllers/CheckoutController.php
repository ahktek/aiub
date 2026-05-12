<?php
// controllers/CheckoutController.php

require_once '../config/database.php';
require_once '../models/Product.php';
require_once '../models/User.php';
require_once '../models/Order.php';
require_once '../models/OrderItem.php';
require_once '../config/helpers.php';

function checkout_action() {
    global $pdo;
    require_login();
    
    if (empty($_SESSION['cart'])) {
        $_SESSION['flash_error'] = "Your cart is empty.";
        header("Location: ?route=cart");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $user = find_user_by_id($pdo, $user_id);
    $saved_addresses = $user['shipping_addresses'] ? json_decode($user['shipping_addresses'], true) : [];

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $address_type = $_POST['address_type']; // 'saved' or 'new'
        $shipping_address = '';
        
        if ($address_type === 'saved') {
            if (!empty($_POST['saved_address'])) {
                $shipping_address = $_POST['saved_address'];
            } else {
                $errors['address'] = "Please select a saved address.";
            }
        } else {
            $new_address = trim($_POST['new_address']);
            if (empty($new_address)) {
                $errors['address'] = "Please enter a new shipping address.";
            } else {
                $shipping_address = $new_address;
            }
        }
        
        $payment_method = $_POST['payment_method'] ?? '';
        if (empty($payment_method)) {
            $errors['payment_method'] = "Please select a payment method.";
        }

        if (empty($errors)) {
            $pdo->beginTransaction();
            
            try {
                // 1. Check stock and calculate grand total
                $grand_total = 0;
                $cart_items = [];
                
                foreach ($_SESSION['cart'] as $product_id => $quantity) {
                    // Lock row for update to prevent race conditions
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                    
                    if (!$product || $product['stock_qty'] < $quantity) {
                        throw new Exception("Not enough stock for product: " . ($product ? $product['name'] : 'Unknown'));
                    }
                    
                    $line_total = $product['price'] * $quantity;
                    $grand_total += $line_total;
                    
                    $cart_items[] = [
                        'product_id' => $product_id,
                        'price' => $product['price'],
                        'quantity' => $quantity
                    ];
                }
                
                // 2. Create Order
                $order_id = create_order($pdo, $user_id, $shipping_address, $payment_method, $grand_total);
                if (!$order_id) {
                    throw new Exception("Failed to create order.");
                }
                
                // 3. Create Order Items & Update Stock
                foreach ($cart_items as $item) {
                    create_order_item($pdo, $order_id, $item['product_id'], $item['quantity'], $item['price']);
                    
                    // Decrement stock
                    $stmt = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
                
                $pdo->commit();
                
                // 4. Clear cart
                $_SESSION['cart'] = [];
                
                // Redirect to success
                $_SESSION['flash_message'] = "Order placed successfully! Order ID: #$order_id";
                header("Location: ?route=my-orders");
                exit;
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors['general'] = $e->getMessage();
            }
        }
    }

    $content_view = '../views/shop/checkout.php';
    require '../views/layout.php';
}
?>
