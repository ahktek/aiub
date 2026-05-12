<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'partials/navbar.php'; ?>

    <main class="container">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo htmlspecialchars($_SESSION['flash_message']); 
                    unset($_SESSION['flash_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo htmlspecialchars($_SESSION['flash_error']); 
                    unset($_SESSION['flash_error']);
                ?>
            </div>
        <?php endif; ?>

        <?php require $content_view; ?>
    </main>

    <footer style="text-align: center; padding: 2rem; color: var(--text-secondary); border-top: 1px solid var(--border-color); background-color: var(--surface-color);">
        <p>&copy; <?php echo date('Y'); ?> E-Commerce Lab Project</p>
    </footer>
    
    <script src="app.js"></script>
</body>
</html>
