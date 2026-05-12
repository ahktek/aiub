<?php
// config/helpers.php

// Admin gate helper
function require_admin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['flash_error'] = "You must be an administrator to access this page.";
        header("Location: ?route=login");
        exit;
    }
}

// Ensure the user is logged in
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['flash_error'] = "Please log in to continue.";
        header("Location: ?route=login");
        exit;
    }
}
?>
