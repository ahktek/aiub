<?php
// controllers/AdminProductController.php

require_once '../config/database.php';
require_once '../models/Product.php';
require_once '../models/Category.php';
require_once '../config/helpers.php';

function admin_products_action() {
    global $pdo;
    require_admin();

    $error = '';
    $success = '';

    // Create uploads directory if it doesn't exist
    $upload_dir = __DIR__ . '/../public/uploads/products/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'create' || $_POST['action'] === 'edit') {
                $id = isset($_POST['id']) ? $_POST['id'] : null;
                $name = trim($_POST['name']);
                $category_id = $_POST['category_id'];
                $description = trim($_POST['description']);
                $price = floatval($_POST['price']);
                $stock_qty = intval($_POST['stock_qty']);
                
                $image_path = null;

                // Handle Image Upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['image']['tmp_name'];
                    $file_name = $_FILES['image']['name'];
                    $file_size = $_FILES['image']['size'];
                    $file_type = $_FILES['image']['type'];
                    
                    $allowed_types = ['image/jpeg', 'image/png'];
                    $max_size = 3 * 1024 * 1024; // 3 MB

                    if (!in_array($file_type, $allowed_types)) {
                        $error = "Only JPEG and PNG files are allowed.";
                    } elseif ($file_size > $max_size) {
                        $error = "File size exceeds 3MB limit.";
                    } else {
                        // Generate unique filename
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $new_filename = uniqid('prod_') . '.' . $ext;
                        $destination = $upload_dir . $new_filename;
                        
                        if (move_uploaded_file($file_tmp, $destination)) {
                            $image_path = 'uploads/products/' . $new_filename;
                        } else {
                            $error = "Failed to upload image.";
                        }
                    }
                }

                if (empty($error)) {
                    if ($_POST['action'] === 'create') {
                        if (create_product($pdo, $category_id, $name, $description, $price, $stock_qty, $image_path)) {
                            $success = "Product created successfully.";
                        } else {
                            $error = "Failed to create product.";
                        }
                    } else {
                        if (update_product($pdo, $id, $category_id, $name, $description, $price, $stock_qty, $image_path)) {
                            $success = "Product updated successfully.";
                        } else {
                            $error = "Failed to update product.";
                        }
                    }
                }

            } elseif ($_POST['action'] === 'delete') {
                $id = $_POST['id'];
                if (delete_product($pdo, $id)) {
                    $success = "Product deleted successfully.";
                } else {
                    $error = "Cannot delete product: It is linked to existing orders.";
                }
            }
        }
    }

    $products = get_all_products_admin($pdo);
    $categories = get_all_categories($pdo);
    
    // For edit form prepopulation
    $edit_product = null;
    if (isset($_GET['edit_id'])) {
        $edit_product = get_product_by_id($pdo, $_GET['edit_id']);
    }

    $content_view = '../views/admin/products.php';
    require '../views/layout.php';
}

function api_toggle_availability() {
    global $pdo;
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $product = get_product_by_id($pdo, $id);
            if ($product) {
                // Toggle
                $new_status = $product['is_available'] ? 0 : 1;
                if (update_product_availability($pdo, $id, $new_status)) {
                    echo json_encode(['ok' => true, 'is_available' => $new_status]);
                    exit;
                }
            }
        }
    }
    
    http_response_code(400);
    echo json_encode(['error' => 'Bad request']);
    exit;
}
?>
