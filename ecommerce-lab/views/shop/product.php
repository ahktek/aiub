<div class="product-detail-layout">
    <div class="detail-img-wrapper">
        <?php if ($product['primary_image_path']): ?>
            <img src="uploads/products/<?php echo htmlspecialchars($product['primary_image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
            <div style="height: 100%; min-height: 400px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                <em>No Image Available</em>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="detail-info">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="detail-category">Category</div>
        <div class="detail-price">$<?php echo number_format($product['price'], 2); ?></div>
        
        <div style="margin-bottom: 1.5rem; color: #fbbf24;">
            <?php 
                if ($avg_rating > 0) {
                    echo str_repeat('★', round($avg_rating)) . str_repeat('☆', 5 - round($avg_rating));
                    echo " <span style='color: var(--text-secondary); font-size: 0.9rem;'>(" . number_format($avg_rating, 1) . " / 5)</span>";
                } else {
                    echo '☆☆☆☆☆';
                }
            ?>
        </div>
        
        <div style="margin-bottom: 2rem;">
            <?php if ($product['stock_qty'] > 0): ?>
                <span class="badge badge-success">In Stock</span>
            <?php else: ?>
                <span class="badge badge-danger">Out of Stock</span>
            <?php endif; ?>
        </div>
        
        <div class="detail-description">
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
        </div>
        
        <?php if ($product['stock_qty'] > 0): ?>
            <div>
                <button id="add-to-cart-btn" data-id="<?php echo $product['id']; ?>" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1.1rem;">Add to Cart</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 4rem;">
    <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 2rem;">Customer Reviews</h3>
    <div id="reviews-container">
        <!-- Reviews loaded via AJAX -->
        <p style="color: var(--text-secondary);">Loading reviews...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            const productId = addToCartBtn.getAttribute('data-id');
            
            fetch(`?route=api/cart/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    document.getElementById('cart-count').textContent = data.count;
                    alert('Product added to cart!');
                } else {
                    alert(data.error || 'Failed to add product to cart.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('A network error occurred.');
            });
        });
    }

    // Load reviews
    const productId = <?php echo $product['id']; ?>;
    fetch(`?route=api/products/reviews&id=${productId}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('reviews-container');
            container.innerHTML = '';
            
            if (data.reviews.length === 0) {
                container.innerHTML = '<p style="color: var(--text-secondary);">No reviews yet. Be the first to review this product after purchasing!</p>';
                return;
            }
            
            data.reviews.forEach(r => {
                const reviewCard = document.createElement('div');
                reviewCard.className = 'review-card';
                
                let ratingHtml = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
                
                reviewCard.innerHTML = `
                    <div class="review-header">
                        <span class="review-author">${r.customer_name}</span>
                        <span style="color: #fbbf24;">${ratingHtml}</span>
                    </div>
                    <p style="margin-bottom: 0.5rem; color: var(--text-secondary);">${r.review_text ? r.review_text : '<em>No written review</em>'}</p>
                    <span class="review-date">${r.created_at}</span>
                `;
                container.appendChild(reviewCard);
            });
        })
        .catch(err => console.error(err));
});
</script>
