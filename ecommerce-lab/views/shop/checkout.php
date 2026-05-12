<div class="checkout-container">
    <div class="card">
        <h2 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Checkout</h2>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
        <?php endif; ?>

        <form action="?route=checkout" method="POST">
            
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--text-secondary); font-size: 1.1rem;">1. Shipping Address</h3>
                <?php if (isset($errors['address'])): ?>
                    <span class="error-text"><?php echo htmlspecialchars($errors['address']); ?></span>
                <?php endif; ?>
                
                <div class="form-group" style="background-color: var(--bg-color); padding: 1.5rem; border-radius: var(--border-radius); border: 1px solid var(--border-color);">
                    <?php if (!empty($saved_addresses)): ?>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="radio" id="addr_saved" name="address_type" value="saved" checked>
                                <span style="font-weight: 600;">Use a saved address</span>
                            </label>
                            <div style="margin-left: 1.5rem; margin-top: 1rem; display: flex; flex-direction: column; gap: 0.75rem;">
                                <?php foreach ($saved_addresses as $index => $addr): ?>
                                    <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; padding: 0.75rem; background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: 4px;">
                                        <input type="radio" name="saved_address" value="<?php echo htmlspecialchars($addr); ?>" <?php echo $index === 0 ? 'checked' : ''; ?> style="margin-top: 0.2rem;">
                                        <span style="color: var(--text-secondary); line-height: 1.4;"><?php echo htmlspecialchars($addr); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" id="addr_new" name="address_type" value="new" <?php echo empty($saved_addresses) ? 'checked' : ''; ?>>
                            <span style="font-weight: 600;">Use a new address</span>
                        </label>
                        <div id="new_address_div" style="margin-left: 1.5rem; margin-top: 1rem; <?php echo empty($saved_addresses) ? 'display: block;' : 'display: none;'; ?>">
                            <textarea name="new_address" rows="3" class="form-control" placeholder="Enter your full shipping address here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--text-secondary); font-size: 1.1rem;">2. Payment Method</h3>
                <?php if (isset($errors['payment_method'])): ?>
                    <span class="error-text"><?php echo htmlspecialchars($errors['payment_method']); ?></span>
                <?php endif; ?>
                
                <div class="form-group" style="display: flex; gap: 1rem;">
                    <label style="flex: 1; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background-color: var(--surface-color);">
                        <input type="radio" name="payment_method" value="Cash" checked>
                        <span>Cash on Delivery</span>
                    </label>
                    <label style="flex: 1; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); background-color: var(--surface-color);">
                        <input type="radio" name="payment_method" value="Card">
                        <span>Credit/Debit Card</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 1rem; font-size: 1.1rem; margin-top: 1rem;">Place Order</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addrSavedRadio = document.getElementById('addr_saved');
    const addrNewRadio = document.getElementById('addr_new');
    const newAddressDiv = document.getElementById('new_address_div');

    if (addrSavedRadio && addrNewRadio) {
        addrSavedRadio.addEventListener('change', () => {
            if (addrSavedRadio.checked) newAddressDiv.style.display = 'none';
        });
        addrNewRadio.addEventListener('change', () => {
            if (addrNewRadio.checked) newAddressDiv.style.display = 'block';
        });
    }
});
</script>
