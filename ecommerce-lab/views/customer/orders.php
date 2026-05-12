<h2 style="margin-bottom: 2rem;">My Orders</h2>

<?php if (empty($orders)): ?>
    <div class="card" style="text-align: center; padding: 4rem;">
        <p style="margin-bottom: 1rem; color: var(--text-secondary);">You haven't placed any orders yet.</p>
        <a href="?route=home" class="btn btn-primary">Start shopping!</a>
    </div>
<?php else: ?>
    <div class="orders-list">
        <?php foreach ($orders as $o): ?>
            <?php 
                $status_class = 'badge-neutral'; 
                if ($o['status'] == 'Pending') $status_class = 'badge-warning';
                if ($o['status'] == 'Processing') $status_class = 'badge-info';
                if ($o['status'] == 'Shipped') $status_class = 'badge-primary';
                if ($o['status'] == 'Delivered') $status_class = 'badge-success';
                if ($o['status'] == 'Cancelled') $status_class = 'badge-danger';
            ?>
            <div class="order-card">
                <div class="order-header" onclick="toggleOrder(<?php echo $o['id']; ?>)">
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <span style="font-weight: 600; font-size: 1.1rem;">Order #<?php echo $o['id']; ?></span>
                        <span style="color: var(--text-secondary); font-size: 0.9rem;"><?php echo date('M d, Y H:i', strtotime($o['created_at'])); ?></span>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 2rem;">
                        <span style="font-weight: 600;">$<?php echo number_format($o['total_amount'], 2); ?></span>
                        <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($o['status']); ?></span>
                        <svg id="icon-<?php echo $o['id']; ?>" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </div>
                
                <div id="order-details-<?php echo $o['id']; ?>" class="order-details-pane">
                    <h4 style="margin-bottom: 1rem; font-size: 1rem; color: var(--text-secondary);">Order Items</h4>
                    <div class="table-responsive">
                        <table style="box-shadow: none; border: 1px solid var(--border-color);">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <?php if ($o['status'] === 'Delivered'): ?>
                                        <th style="text-align: right;">Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($o['items'] as $item): ?>
                                    <tr>
                                        <td style="font-weight: 500;"><a href="?route=product&id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['product_name']); ?></a></td>
                                        <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td style="font-weight: 500;">$<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                                        <?php if ($o['status'] === 'Delivered'): ?>
                                            <td style="text-align: right;">
                                                <?php if ($item['has_reviewed']): ?>
                                                    <span style="color: var(--status-success); font-size: 0.85rem; font-weight: 500;">Reviewed ✓</span>
                                                <?php else: ?>
                                                    <button class="btn btn-outline btn-sm" onclick="openReviewModal(<?php echo $item['product_id']; ?>, '<?php echo htmlspecialchars(addslashes($item['product_name'])); ?>')">Write Review</button>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem; background-color: var(--surface-color); padding: 1rem; border-radius: var(--border-radius); border: 1px solid var(--border-color);">
                        <h4 style="font-size: 0.95rem; margin-bottom: 0;">Shipping Details</h4>
                        <p style="color: var(--text-secondary); font-size: 0.95rem;"><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($o['shipping_address'])); ?></p>
                        <p style="color: var(--text-secondary); font-size: 0.95rem;"><strong>Payment Method:</strong> <?php echo htmlspecialchars($o['payment_method']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Review Modal -->
<div id="review-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="card" style="background:#fff; padding:2rem; border-radius:8px; width:400px; max-width:90%;">
        <h3 id="review-product-name" style="margin-bottom: 1rem;"></h3>
        <form id="review-form">
            <input type="hidden" id="review-product-id">
            
            <div class="form-group">
                <label class="form-label">Rating (1-5)</label>
                <select id="review-rating" class="form-control" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Terrible</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Review Text (Optional)</label>
                <textarea id="review-text" class="form-control" rows="4"></textarea>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top: 1.5rem;">
                <button type="button" class="btn btn-outline" onclick="closeReviewModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleOrder(id) {
    const pane = document.getElementById('order-details-' + id);
    const icon = document.getElementById('icon-' + id);
    
    if (pane.style.display === 'block') {
        pane.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    } else {
        pane.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    }
}

function openReviewModal(productId, productName) {
    document.getElementById('review-product-id').value = productId;
    document.getElementById('review-product-name').textContent = 'Review: ' + productName;
    document.getElementById('review-rating').value = '5';
    document.getElementById('review-text').value = '';
    
    document.getElementById('review-modal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('review-modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const productId = document.getElementById('review-product-id').value;
            const rating = document.getElementById('review-rating').value;
            const text = document.getElementById('review-text').value;
            
            fetch('?route=api/reviews/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    product_id: productId,
                    rating: rating,
                    review_text: text
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alert('Review submitted successfully!');
                    location.reload();
                } else {
                    alert(data.error || 'Failed to submit review.');
                }
            })
            .catch(err => console.error(err));
        });
    }
});
</script>
