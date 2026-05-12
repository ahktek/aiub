<?php
include "../config/db.php";

$name = "";
$email = "";
$phone = "";
$password = "";
$confirm_password = "";

// Registration Logic
if(isset($_POST['register']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password)
    {
        Header("Location: ../views/auth/register.php?error=Passwords do not match");
        exit();
    }

    if(strlen($password) < 8)
    {
        Header("Location: ../views/auth/register.php?error=Password must be at least 8 characters");
        exit();
    }

    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if($result->num_rows > 0)
    {
        Header("Location: ../views/auth/register.php?error=Email already registered");
        exit();
    }
    $check_stmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'customer';

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $password_hash, $role);
    
    if($stmt->execute())
    {
        Header("Location: ../views/auth/login.php?success=Registration successful! Please login.");
    }
    else
    {
        Header("Location: ../views/auth/register.php?error=Registration failed");
    }
    $stmt->close();
}

// Login Logic
if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user && password_verify($password, $user['password_hash']))
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        if($user['role'] == 'admin')
        {
            Header("Location: ../views/admin/dashboard.php");
        }
        else
        {
            Header("Location: ../views/shop/profile.php");
        }
    }
    else
    {
        Header("Location: ../views/auth/login.php?error=Invalid email or password");
    }
    $stmt->close();
}

// Logout Logic
if(isset($_GET['logout']))
{
    session_destroy();
    Header("Location: ../views/auth/login.php");
    exit();
}
?>
