<?php
// controllers/ShopController.php

require_once '../config/database.php';
require_once '../models/Product.php';
require_once '../models/Category.php';
require_once '../config/helpers.php';

// Common function for fetching catalogue products
function get_catalogue_products($pdo, $category_id = null, $search_query = null) {
    $sql = "
        SELECT p.*, c.name as category_name,
        (SELECT COALESCE(AVG(rating), 0) FROM reviews r WHERE r.product_id = p.id) as avg_rating
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.is_available = 1
    ";
    
    $params = [];
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    if ($search_query) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
    }
    
    $sql .= " ORDER BY p.id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function catalogue_action() {
    global $pdo;
    require_login();

    $categories = get_all_categories($pdo);
    $products = get_catalogue_products($pdo);

    $content_view = '../views/shop/catalogue.php';
    require '../views/layout.php';
}

function product_detail_action() {
    global $pdo;
    require_login();

    if (!isset($_GET['id'])) {
        header("Location: ?route=home");
        exit;
    }

    $id = $_GET['id'];
    $product = get_product_by_id($pdo, $id);

    if (!$product || !$product['is_available']) {
        $_SESSION['flash_error'] = "Product not found or unavailable.";
        header("Location: ?route=home");
        exit;
    }

    // Get average rating
    $stmt = $pdo->prepare("SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE product_id = ?");
    $stmt->execute([$id]);
    $avg_rating = $stmt->fetchColumn();

    $content_view = '../views/shop/product.php';
    require '../views/layout.php';
}

function api_search_action() {
    global $pdo;
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? $_GET['category_id'] : null;
    $q = isset($_GET['q']) ? trim($_GET['q']) : null;

    $products = get_catalogue_products($pdo, $category_id, $q);
    
    // Format numeric values
    foreach ($products as &$p) {
        $p['price_formatted'] = number_format($p['price'], 2);
        $p['rating_formatted'] = number_format($p['avg_rating'], 1);
    }
    
    echo json_encode($products);
    exit;
}
?>
