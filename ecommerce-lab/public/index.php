<?php
// basic router
session_start();

$route = isset($_GET['route']) ? $_GET['route'] : 'home';

switch ($route) {
    case 'home':
        require_once '../controllers/ShopController.php';
        catalogue_action();
        break;
        
    // Task 1: Auth
    case 'register':
        require_once '../controllers/AuthController.php';
        register_action();
        break;
    case 'login':
        require_once '../controllers/AuthController.php';
        login_action();
        break;
    case 'logout':
        require_once '../controllers/AuthController.php';
        logout_action();
        break;
    case 'profile':
        require_once '../controllers/ProfileController.php';
        profile_action();
        break;
        
    // Task 2: Admin
    case 'admin/categories':
        require_once '../controllers/AdminCategoryController.php';
        admin_categories_action();
        break;
    case 'admin/products':
        require_once '../controllers/AdminProductController.php';
        admin_products_action();
        break;
        
    // Task 3: Shop & Cart
    case 'product':
        require_once '../controllers/ShopController.php';
        product_detail_action();
        break;
    case 'cart':
        require_once '../controllers/CartController.php';
        cart_view_action();
        break;
    case 'checkout':
        require_once '../controllers/CheckoutController.php';
        checkout_action();
        break;
        
    // Task 4: Orders & Reviews
    case 'my-orders':
        require_once '../controllers/CustomerOrderController.php';
        customer_orders_action();
        break;
    case 'admin/orders':
        require_once '../controllers/AdminOrderController.php';
        admin_orders_action();
        break;
        
    // API routes (AJAX)
    case 'api/products/search':
        require_once '../controllers/ShopController.php';
        api_search_action();
        break;
    case 'api/products/availability':
        require_once '../controllers/AdminProductController.php';
        api_toggle_availability();
        break;
    case 'api/cart/add':
        require_once '../controllers/CartController.php';
        api_cart_add();
        break;
    case 'api/cart/update':
        require_once '../controllers/CartController.php';
        api_cart_update();
        break;
    case 'api/cart/remove':
        require_once '../controllers/CartController.php';
        api_cart_remove();
        break;
    case 'api/orders/update':
        require_once '../controllers/AdminOrderController.php';
        api_update_order_status();
        break;
    case 'api/reviews':
        require_once '../controllers/ReviewController.php';
        api_leave_review();
        break;
    case 'api/products/reviews':
        require_once '../controllers/ReviewController.php';
        api_get_reviews();
        break;
        
    default:
        http_response_code(404);
        echo "404 Page Not Found";
        break;
}
?>
