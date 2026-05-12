<h2>My Profile</h2>

<?php if (!empty($success)): ?>
    <div style="background-color: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin-bottom: 15px;">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<div style="display: flex; gap: 40px; flex-wrap: wrap;">
    <!-- Profile Info Form -->
    <div style="flex: 1; min-width: 300px;">
        <h3>Update Information</h3>
        <?php if (isset($errors['general'])): ?>
            <div class="error"><?php echo htmlspecialchars($errors['general']); ?></div>
        <?php endif; ?>

        <form action="?route=profile" method="POST">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                <?php if (isset($errors['name'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['name']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                <?php if (isset($errors['email'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['email']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>

            <h4>Shipping Addresses (Max 2)</h4>
            <div class="form-group">
                <label>Address 1:</label>
                <input type="text" name="address1" value="<?php echo isset($saved_addresses[0]) ? htmlspecialchars($saved_addresses[0]) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Address 2:</label>
                <input type="text" name="address2" value="<?php echo isset($saved_addresses[1]) ? htmlspecialchars($saved_addresses[1]) : ''; ?>">
            </div>

            <button type="submit">Save Profile</button>
        </form>
    </div>

    <!-- Change Password Form -->
    <div style="flex: 1; min-width: 300px;">
        <h3>Change Password</h3>
        <?php if (isset($errors['password_general'])): ?>
            <div class="error"><?php echo htmlspecialchars($errors['password_general']); ?></div>
        <?php endif; ?>

        <form action="?route=profile" method="POST">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password">
                <?php if (isset($errors['current_password'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['current_password']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password">
                <?php if (isset($errors['new_password'])): ?>
                    <span class="error"><?php echo htmlspecialchars($errors['new_password']); ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Change Password</button>
        </form>
    </div>
</div>
