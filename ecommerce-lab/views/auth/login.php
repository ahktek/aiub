<div class="card card-centered">
    <h2 style="text-align: center; margin-bottom: 2rem;">Login</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; text-align: center;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="?route=login" method="POST">
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; cursor: pointer;">
            <input type="checkbox" id="remember" name="remember" style="width: auto; cursor: pointer;">
            <label for="remember" style="margin: 0; cursor: pointer; color: var(--text-secondary);">Remember Me</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem; font-size: 1.05rem; padding: 0.85rem;">Login</button>
    </form>
    
    <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        <p style="color: var(--text-secondary);">Don't have an account? <a href="?route=register" style="font-weight: 500;">Register here</a></p>
    </div>
</div>
