<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Commerce Store</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background-color: #fff;">
    <div style="padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
        <div style="display: flex; align-items: center; gap: 10px; color: var(--primary-blue); font-weight: 700; font-size: 18px;">
            <i class="fas fa-shopping-bag"></i> E-Commerce Store
        </div>
        <div style="font-size: 14px;">
            Already have an account? <a href="login.php" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">Login</a>
        </div>
    </div>

    <div style="max-width: 900px; margin: 60px auto; padding: 0 20px;">
        <h1 style="font-size: 28px; margin-bottom: 10px;">Create Your Account</h1>
        <p style="color: var(--text-muted); margin-bottom: 40px;">Join us today and enjoy shopping</p>

        <?php if(isset($_GET['error'])): ?>
            <p style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="../../controllers/AuthController.php">
            <div class="grid-2">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-icon-wrapper">
                        <i class="far fa-user"></i>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="far fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number (Optional)</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-phone"></i>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number">
                    </div>
                </div>

                <div style="background-color: #f1f5f9; padding: 20px; border-radius: 8px; font-size: 13px; color: var(--text-muted);">
                    <ul style="list-style: none;">
                        <li><i class="fas fa-check-circle" style="color: #cbd5e1; margin-right: 8px;"></i> Password must be at least 8 characters</li>
                        <li><i class="fas fa-check-circle" style="color: #cbd5e1; margin-right: 8px;"></i> Include uppercase, lowercase, number & symbol</li>
                    </ul>
                </div>
            </div>

            <button type="submit" name="register" class="btn btn-green" style="margin-top: 30px;">Create Account</button>
            
            <p style="text-align: center; font-size: 12px; color: var(--text-muted); margin-top: 20px;">
                By creating an account, you agree to our <a href="#" style="color: var(--primary-blue);">Terms & Conditions</a> and <a href="#" style="color: var(--primary-blue);">Privacy Policy</a>
            </p>
        </form>
    </div>
    <script>
        document.addEventListener('click', function(e) {
            console.log('Click detected on:', e.target);
        });
    </script>
</body>
</html>
