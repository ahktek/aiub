<?php
// controllers/ProfileController.php

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../config/helpers.php';

function profile_action() {
    global $pdo;
    require_login();
    
    $user_id = $_SESSION['user_id'];
    $user = find_user_by_id($pdo, $user_id);
    $errors = [];
    $success = '';

    // Handle Profile Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        $addr1 = trim($_POST['address1']);
        $addr2 = trim($_POST['address2']);
        
        $addresses = [];
        if (!empty($addr1)) $addresses[] = $addr1;
        if (!empty($addr2)) $addresses[] = $addr2;
        
        $shipping_addresses_json = json_encode($addresses);

        if (empty($name)) {
            $errors['name'] = "Name is required.";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Valid email is required.";
        } else {
            // Check if email belongs to another user
            $existing = find_user_by_email($pdo, $email);
            if ($existing && $existing['id'] !== $user_id) {
                $errors['email'] = "Email is already taken.";
            }
        }

        if (empty($errors)) {
            if (update_user_profile($pdo, $user_id, $name, $email, $phone, $shipping_addresses_json)) {
                $success = "Profile updated successfully.";
                $_SESSION['name'] = $name; // update session name
                $user = find_user_by_id($pdo, $user_id); // refresh user data
            } else {
                $errors['general'] = "Failed to update profile.";
            }
        }
    }

    // Handle Password Change
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        if (!password_verify($current_password, $user['password_hash'])) {
            $errors['current_password'] = "Incorrect current password.";
        }
        if (strlen($new_password) < 8) {
            $errors['new_password'] = "New password must be at least 8 characters.";
        }

        if (empty($errors)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if (update_user_password($pdo, $user_id, $hashed_password)) {
                $success = "Password changed successfully.";
            } else {
                $errors['password_general'] = "Failed to change password.";
            }
        }
    }

    $saved_addresses = $user['shipping_addresses'] ? json_decode($user['shipping_addresses'], true) : [];
    
    $content_view = '../views/profile/edit.php';
    require '../views/layout.php';
}
?>
