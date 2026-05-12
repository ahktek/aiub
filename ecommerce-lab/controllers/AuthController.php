<?php
// controllers/AuthController.php

require_once '../config/database.php';
require_once '../models/User.php';

function check_remember_me($pdo) {
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $user = find_user_by_token($pdo, hash('sha256', $token));
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
        }
    }
}

// Call check_remember_me globally when auth controller is included
check_remember_me($pdo);

function register_action() {
    global $pdo;
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];

        if (empty($name)) {
            $errors['name'] = "Name is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Valid email is required.";
        } else {
            // Check if email already exists
            if (find_user_by_email($pdo, $email)) {
                $errors['email'] = "Email is already registered.";
            }
        }
        if (strlen($password) < 8) {
            $errors['password'] = "Password must be at least 8 characters long.";
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // STUDENT HACK: If name is 'Admin', set role to admin automatically for easier testing
            $role = ($name === 'Admin') ? 'admin' : 'customer';

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $hashed_password, $role])) {
                $_SESSION['flash_message'] = "Registration successful as $role. Please log in.";
                header("Location: ?route=login");
                exit;
            } else {
                $errors['general'] = "Failed to register user.";
            }
        }
    }

    $content_view = '../views/auth/register.php';
    require '../views/layout.php';
}

function login_action() {
    global $pdo;
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);

        $user = find_user_by_email($pdo, $email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $token_hash = hash('sha256', $token);
                update_remember_token($pdo, $user['id'], $token_hash);
                // Cookie lasts for 30 days
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
            }

            if ($user['role'] === 'admin') {
                header("Location: ?route=admin/orders");
            } else {
                header("Location: ?route=home");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }

    $content_view = '../views/auth/login.php';
    require '../views/layout.php';
}

function logout_action() {
    global $pdo;
    if (isset($_SESSION['user_id'])) {
        update_remember_token($pdo, $_SESSION['user_id'], null);
    }
    
    $_SESSION = [];
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    
    session_start();
    $_SESSION['flash_message'] = "You have been logged out.";
    header("Location: ?route=login");
    exit;
}
?>
