<?php
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
$current_route = isset($_GET['route']) ? $_GET['route'] : 'home';
?>
<nav class="navbar">
    <div class="nav-brand">
        <a href="?route=home">ShopEase</a>
    </div>
    
    <div class="nav-links">
        <?php if ($is_admin): ?>
            <!-- Admin Links -->
            <a href="?route=home" class="nav-link <?php echo $current_route === 'home' ? 'active' : ''; ?>">Dashboard</a>
            <a href="?route=admin/categories" class="nav-link <?php echo $current_route === 'admin/categories' ? 'active' : ''; ?>">Categories</a>
            <a href="?route=admin/products" class="nav-link <?php echo $current_route === 'admin/products' ? 'active' : ''; ?>">Products</a>
            <a href="?route=admin/orders" class="nav-link <?php echo $current_route === 'admin/orders' ? 'active' : ''; ?>">All Orders</a>
            <a href="?route=logout" class="nav-link">Logout</a>
        <?php else: ?>
            <!-- Customer Links -->
            <a href="?route=home" class="nav-link <?php echo $current_route === 'home' ? 'active' : ''; ?>">Home</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?route=my-orders" class="nav-link <?php echo $current_route === 'my-orders' ? 'active' : ''; ?>">My Orders</a>
                <a href="?route=profile" class="nav-link <?php echo $current_route === 'profile' ? 'active' : ''; ?>">Profile</a>
                <a href="?route=logout" class="nav-link">Logout</a>
                <a href="?route=cart" class="nav-cart-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="cart-badge" id="cart-count"><?php echo $cart_count; ?></span>
                </a>
            <?php else: ?>
                <a href="?route=login" class="nav-link <?php echo $current_route === 'login' ? 'active' : ''; ?>">Login</a>
                <a href="?route=register" class="nav-link <?php echo $current_route === 'register' ? 'active' : ''; ?>">Register</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</nav>
