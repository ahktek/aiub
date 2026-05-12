<div class="card card-centered">
    <h2 style="text-align: center; margin-bottom: 2rem;">Register</h2>

    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; text-align: center;"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>

    <form action="?route=register" method="POST">
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <?php if (isset($errors['name'])): ?>
                <span class="error-text"><?php echo htmlspecialchars($errors['name']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <?php if (isset($errors['email'])): ?>
                <span class="error-text"><?php echo htmlspecialchars($errors['email']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone" class="form-label">Phone <span style="color: var(--text-secondary); font-weight: normal;">(Optional)</span></label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control">
            <?php if (isset($errors['password'])): ?>
                <span class="error-text"><?php echo htmlspecialchars($errors['password']); ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem; font-size: 1.05rem; padding: 0.85rem;">Create Account</button>
    </form>
    
    <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        <p style="color: var(--text-secondary);">Already have an account? <a href="?route=login" style="font-weight: 500;">Login here</a></p>
    </div>
</div>
