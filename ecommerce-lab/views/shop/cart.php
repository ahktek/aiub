<h2 style="margin-bottom: 2rem;">Shopping Cart</h2>

<div id="cart-container">
    <?php if (empty($cart_items)): ?>
        <div class="card" style="text-align: center; padding: 4rem;">
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">Your cart is empty.</p>
            <a href="?route=home" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items table-responsive">
                <table id="cart-table">
                    <thead>
                        <tr>
                            <th colspan="2">Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr id="cart-row-<?php echo $item['product_id']; ?>">
                                <td style="width: 60px;">
                                    <?php if ($item['primary_image_path']): ?>
                                        <img src="uploads/products/<?php echo htmlspecialchars($item['primary_image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="table-img">
                                    <?php else: ?>
                                        <div class="table-img" style="background-color: #eee; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #999;">No Img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?route=product&id=<?php echo $item['product_id']; ?>" style="font-weight: 500; color: var(--text-primary);">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <div class="qty-control">
                                        <button class="btn btn-outline btn-sm qty-btn" data-action="dec" data-id="<?php echo $item['product_id']; ?>">-</button>
                                        <span id="qty-<?php echo $item['product_id']; ?>" style="font-weight: 500; min-width: 20px; text-align: center;"><?php echo $item['quantity']; ?></span>
                                        <button class="btn btn-outline btn-sm qty-btn" data-action="inc" data-id="<?php echo $item['product_id']; ?>">+</button>
                                    </div>
                                </td>
                                <td style="font-weight: 600;">$<span id="line-total-<?php echo $item['product_id']; ?>"><?php echo number_format($item['line_total'], 2); ?></span></td>
                                <td style="text-align: right;">
                                    <button class="btn btn-outline btn-sm remove-btn" data-id="<?php echo $item['product_id']; ?>" style="color: var(--status-danger); border-color: transparent;">&times;</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="cart-summary">
                <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Order Summary</h3>
                <div class="summary-row">
                    <span style="color: var(--text-secondary);">Subtotal</span>
                    <span style="font-weight: 500;">$<span id="grand-total-sub"><?php echo number_format($grand_total, 2); ?></span></span>
                </div>
                <div class="summary-row">
                    <span style="color: var(--text-secondary);">Shipping</span>
                    <span style="font-weight: 500;">Calculated at checkout</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span style="color: var(--accent-color);">$<span id="grand-total"><?php echo number_format($grand_total, 2); ?></span></span>
                </div>
                
                <a href="?route=checkout" class="btn btn-primary btn-block" style="padding: 1rem; font-size: 1.1rem;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // Update Quantity
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const action = this.getAttribute('data-action');
            
            fetch('?route=api/cart/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: id, action: action })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    if (data.new_qty === 0) {
                        document.getElementById(`cart-row-${id}`).remove();
                    } else {
                        document.getElementById(`qty-${id}`).textContent = data.new_qty;
                        document.getElementById(`line-total-${id}`).textContent = data.line_total.toFixed(2);
                    }
                    document.getElementById('grand-total').textContent = data.grand_total.toFixed(2);
                    if(document.getElementById('grand-total-sub')) {
                        document.getElementById('grand-total-sub').textContent = data.grand_total.toFixed(2);
                    }
                    document.getElementById('cart-count').textContent = data.count;
                    
                    if (data.count === 0) {
                        location.reload(); // Reload to show empty cart message
                    }
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(err => console.error(err));
        });
    });

    // Remove Item
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!confirm('Remove this item from cart?')) return;
            
            fetch('?route=api/cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    document.getElementById(`cart-row-${id}`).remove();
                    document.getElementById('grand-total').textContent = data.grand_total.toFixed(2);
                    if(document.getElementById('grand-total-sub')) {
                        document.getElementById('grand-total-sub').textContent = data.grand_total.toFixed(2);
                    }
                    document.getElementById('cart-count').textContent = data.count;
                    
                    if (data.count === 0) {
                        location.reload();
                    }
                }
            })
            .catch(err => console.error(err));
        });
    });

});
</script>
