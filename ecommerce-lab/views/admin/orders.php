<h2 style="margin-bottom: 2rem;">Manage Orders</h2>

<div class="card" style="margin-bottom: 2rem;">
    <form action="?route=admin/orders" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <input type="hidden" name="route" value="admin/orders">
        
        <div style="flex: 1; min-width: 200px;">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>
        
        <div style="flex: 1; min-width: 200px;">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>
        
        <div style="flex: 1; min-width: 200px;">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="Processing" <?php echo $status_filter === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Filter</button>
        </div>
    </form>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 4rem; color: var(--text-secondary);">
            <p>No orders found for the selected criteria.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td style="font-weight: 600;">#<?php echo $o['id']; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($o['created_at'])); ?></td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                <div style="color: var(--text-secondary); font-size: 0.85rem;"><?php echo htmlspecialchars($o['customer_email']); ?></div>
                            </td>
                            <td style="font-weight: 500;">$<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <select class="status-dropdown form-control" data-id="<?php echo $o['id']; ?>" style="width: auto; padding: 0.4rem 2rem 0.4rem 0.8rem; font-size: 0.9rem;">
                                        <option value="Pending" <?php echo $o['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $o['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $o['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $o['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $o['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <span id="status-msg-<?php echo $o['id']; ?>" style="font-size: 0.8em; min-width: 60px;"></span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.status-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const status = this.value;
            const msgSpan = document.getElementById(`status-msg-${id}`);
            
            msgSpan.textContent = 'Saving...';
            msgSpan.style.color = 'orange';

            fetch(`?route=api/orders/update&id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    msgSpan.textContent = 'Saved!';
                    msgSpan.style.color = 'green';
                    setTimeout(() => { msgSpan.textContent = ''; }, 2000);
                } else {
                    msgSpan.textContent = 'Error';
                    msgSpan.style.color = 'red';
                    alert(data.error || 'Failed to update status.');
                }
            })
            .catch(err => {
                console.error(err);
                msgSpan.textContent = 'Error';
                msgSpan.style.color = 'red';
            });
        });
    });
});
</script>
