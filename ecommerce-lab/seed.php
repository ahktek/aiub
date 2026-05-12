<?php
// seed.php - Run this once to populate categories, products, and images.
require_once __DIR__ . '/config/database.php';

echo "Starting seed process...<br>";

// 1. Duplicate Images
$upload_dir = __DIR__ . '/public/uploads/products/';
$source_image = $upload_dir . 'image1.png';

if (!file_exists($source_image)) {
    die("Error: Source image ($source_image) not found. Please place image1.png in public/uploads/products/ first.");
}

for ($i = 2; $i <= 12; $i++) {
    $target_image = $upload_dir . "image$i.png";
    if (copy($source_image, $target_image)) {
        echo "Copied image1.png to image$i.png<br>";
    } else {
        echo "Failed to copy to image$i.png<br>";
    }
}

// 2. Insert Categories and Products
try {
    // We will not truncate, just insert. If you run this multiple times, you get duplicates.
    
    // Category 1: Electronics
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute(['Electronics']);
    $cat_electronics = $pdo->lastInsertId();

    $products_electronics = [
        ['Wireless Headphones', 'image1.png', 49.99],
        ['Bluetooth Speaker', 'image2.png', 34.99],
        ['USB-C Hub', 'image3.png', 24.99],
        ['Phone Stand', 'image4.png', 12.99]
    ];

    // Category 2: Home & Kitchen
    $stmt->execute(['Home & Kitchen']);
    $cat_home = $pdo->lastInsertId();

    $products_home = [
        ['Coffee Mug Set', 'image5.png', 18.99],
        ['Bamboo Cutting Board', 'image6.png', 22.99],
        ['Spice Rack', 'image7.png', 29.99],
        ['Kitchen Timer', 'image8.png', 14.99]
    ];

    // Category 3: Lifestyle & Accessories
    $stmt->execute(['Lifestyle & Accessories']);
    $cat_lifestyle = $pdo->lastInsertId();

    $products_lifestyle = [
        ['Canvas Tote Bag', 'image9.png', 16.99],
        ['Minimalist Wallet', 'image10.png', 27.99],
        ['Notebook Set', 'image11.png', 19.99],
        ['Keychain Organizer', 'image12.png', 9.99]
    ];

    $stmt_prod = $pdo->prepare("INSERT INTO products (category_id, name, description, price, stock_qty, primary_image_path, is_available) VALUES (?, ?, ?, ?, ?, ?, 1)");

    foreach ($products_electronics as $p) {
        $stmt_prod->execute([$cat_electronics, $p[0], 'A great ' . strtolower($p[0]), $p[2], 100, $p[1]]);
    }
    foreach ($products_home as $p) {
        $stmt_prod->execute([$cat_home, $p[0], 'A great ' . strtolower($p[0]), $p[2], 100, $p[1]]);
    }
    foreach ($products_lifestyle as $p) {
        $stmt_prod->execute([$cat_lifestyle, $p[0], 'A great ' . strtolower($p[0]), $p[2], 100, $p[1]]);
    }

    echo "Successfully inserted categories and products!<br>";
    echo "<a href='public/'>Go to Storefront</a>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
