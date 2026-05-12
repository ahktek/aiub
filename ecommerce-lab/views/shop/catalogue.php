<div class="catalogue-header">
    <h2>Product Catalogue</h2>
    
    <div class="catalogue-filters">
        <input type="text" id="search-input" class="form-control" placeholder="Search products..." style="width: 300px;">
        
        <select id="category-filter" class="form-control" style="width: 200px;">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div id="product-grid" class="product-grid">
    <?php foreach ($products as $p): ?>
        <div class="product-card" onclick="window.location.href='?route=product&id=<?php echo $p['id']; ?>'" style="cursor: pointer;">
            <div class="product-img-wrapper">
                <?php if ($p['primary_image_path']): ?>
                    <img src="uploads/products/<?php echo htmlspecialchars($p['primary_image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                <?php else: ?>
                    <em>No Image</em>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <div class="product-title"><?php echo htmlspecialchars($p['name']); ?></div>
                <div class="product-price">$<?php echo number_format($p['price'], 2); ?></div>
                <div class="product-rating">
                    <?php 
                        $rating = floatval($p['avg_rating']);
                        if ($rating > 0) {
                            echo str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating));
                            echo " <span style='color: var(--text-secondary); font-size: 0.8rem;'>($rating)</span>";
                        } else {
                            echo '☆☆☆☆☆';
                        }
                    ?>
                </div>
                <a href="?route=product&id=<?php echo $p['id']; ?>" class="btn btn-outline" style="margin-top: auto; display: block; text-align: center;">View Details</a>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($products)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);">
            <p>No products available at the moment.</p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const productGrid = document.getElementById('product-grid');

    function fetchProducts() {
        const q = encodeURIComponent(searchInput.value);
        const cat = encodeURIComponent(categoryFilter.value);
        
        fetch(`?route=api/products/search&q=${q}&category_id=${cat}`)
            .then(res => res.json())
            .then(data => {
                productGrid.innerHTML = '';
                if (data.length === 0) {
                    productGrid.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-secondary);"><p>No products found.</p></div>';
                    return;
                }
                
                data.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    card.style.cursor = 'pointer';
                    card.onclick = () => window.location.href = `?route=product&id=${p.id}`;
                    
                    let imgHtml = p.primary_image_path 
                        ? `<img src="public/uploads/products/${p.primary_image_path}" alt="${p.name}">` 
                        : `<em>No Image</em>`;
                        
                    let ratingVal = parseFloat(p.rating_formatted);
                    let ratingHtml = '☆☆☆☆☆';
                    if (ratingVal > 0) {
                        let fullStars = Math.round(ratingVal);
                        ratingHtml = '★'.repeat(fullStars) + '☆'.repeat(5 - fullStars) + ` <span style='color: var(--text-secondary); font-size: 0.8rem;'>(${ratingVal.toFixed(1)})</span>`;
                    }
                        
                    card.innerHTML = `
                        <div class="product-img-wrapper">${imgHtml}</div>
                        <div class="product-info">
                            <div class="product-title">${p.name}</div>
                            <div class="product-price">$${p.price_formatted}</div>
                            <div class="product-rating">${ratingHtml}</div>
                            <a href="?route=product&id=${p.id}" class="btn btn-outline" style="margin-top: auto; display: block; text-align: center;">View Details</a>
                        </div>
                    `;
                    productGrid.appendChild(card);
                });
            })
            .catch(err => console.error(err));
    }

    searchInput.addEventListener('input', fetchProducts);
    categoryFilter.addEventListener('change', fetchProducts);
});
</script>
