<?php
// models/User.php

function find_user_by_email($pdo, $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function find_user_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function create_user($pdo, $name, $email, $phone, $password_hash) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, 'customer')");
    return $stmt->execute([$name, $email, $phone, $password_hash]);
}

function update_remember_token($pdo, $user_id, $token) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
    return $stmt->execute([$token, $user_id]);
}

function find_user_by_token($pdo, $token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);
    return $stmt->fetch();
}

function update_user_profile($pdo, $id, $name, $email, $phone, $shipping_addresses_json) {
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, shipping_addresses = ? WHERE id = ?");
    return $stmt->execute([$name, $email, $phone, $shipping_addresses_json, $id]);
}

function update_user_password($pdo, $id, $password_hash) {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    return $stmt->execute([$password_hash, $id]);
}
?>
