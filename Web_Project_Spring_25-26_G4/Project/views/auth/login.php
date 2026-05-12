<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce Store</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-sidebar">
            <div class="logo-circle">
                <i class="fas fa-shopping-bag fa-3x"></i>
            </div>
            <h1>E-Commerce Store</h1>
            <p>Welcome back! Please login to your account and continue shopping with us.</p>
            <!-- Add a decorative cart image if possible -->
            <div style="margin-top: 50px; opacity: 0.5;">
                <i class="fas fa-shopping-cart fa-10x"></i>
            </div>
        </div>
        <div class="auth-content">
            <div class="auth-card">
                <h2>Login to Your Account</h2>
                <p>Enter your details to access your account</p>
                
                <?php if(isset($_GET['error'])): ?>
                    <p style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                    </p>
                <?php endif; ?>

                <?php if(isset($_GET['success'])): ?>
                    <p style="color: #10b981; background: #dcfce7; padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                    </p>
                <?php endif; ?>
                
                <form method="POST" action="../../controllers/AuthController.php">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon-wrapper">
                            <i class="far fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                        <a href="#" style="color: var(--primary-blue); text-decoration: none;">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-blue">Login</button>
                    
                    <div style="text-align: center; margin: 20px 0; color: var(--text-muted);">
                        Or continue with
                    </div>
                    
                    <div class="social-login">
                        <button type="button" class="btn btn-outline">
                            <img src="https://www.google.com/favicon.ico" width="16"> Google
                        </button>
                        <button type="button" class="btn btn-outline">
                            <i class="fab fa-facebook-f" style="color: #1877f2;"></i> Facebook
                        </button>
                    </div>
                    
                    <div style="text-align: center; font-size: 14px;">
                        Don't have an account? <a href="register.php" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('click', function(e) {
            console.log('Click detected on:', e.target);
        });
    </script>
</body>
</html>
