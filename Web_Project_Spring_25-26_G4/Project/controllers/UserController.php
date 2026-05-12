<?php
include "../config/db.php";

if(!isset($_SESSION['user_id']))
{
    Header("Location: ../views/auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = "";
$email = "";
$phone = "";

// Update Personal Information
if(isset($_POST['update_profile']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
    
    if($stmt->execute())
    {
        $_SESSION['name'] = $name;
        Header("Location: ../views/shop/profile.php?success=Profile updated successfully");
    }
    else
    {
        Header("Location: ../views/shop/profile.php?error=Update failed");
    }
    $stmt->close();
}

// Change Password
if(isset($_POST['change_password']))
{
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if($new_password !== $confirm_password)
    {
        Header("Location: ../views/shop/profile.php?error=Passwords do not match");
        exit();
    }

    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user && password_verify($current_password, $user['password_hash']))
    {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $upd->bind_param("si", $new_hash, $user_id);
        $upd->execute();
        Header("Location: ../views/shop/profile.php?success=Password changed successfully");
    }
    else
    {
        Header("Location: ../views/shop/profile.php?error=Current password incorrect");
    }
    $stmt->close();
}

// Delete Address
if(isset($_GET['delete_address']))
{
    $index = $_GET['delete_address'];
    
    $stmt = $conn->prepare("SELECT shipping_addresses FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $addresses = json_decode($user['shipping_addresses'], true);
    if(!is_array($addresses))
    {
        $addresses = array();
    }
    
    if(isset($addresses[$index]))
    {
        unset($addresses[$index]);
        $addresses = array_values($addresses);
        $json = json_encode($addresses);
        
        $upd = $conn->prepare("UPDATE users SET shipping_addresses = ? WHERE id = ?");
        $upd->bind_param("si", $json, $user_id);
        $upd->execute();
        Header("Location: ../views/shop/profile.php?success=Address deleted");
    }
    else
    {
        Header("Location: ../views/shop/profile.php?error=Address not found");
    }
}

// Add Address
if(isset($_POST['add_address']))
{
    $new_address = $_POST['address'];
    
    $stmt = $conn->prepare("SELECT shipping_addresses FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $addresses = json_decode($user['shipping_addresses'], true);
    if(!is_array($addresses))
    {
        $addresses = array();
    }

    $addresses[] = $new_address;
    $json = json_encode($addresses);
    
    $upd = $conn->prepare("UPDATE users SET shipping_addresses = ? WHERE id = ?");
    $upd->bind_param("si", $json, $user_id);
    $upd->execute();
    Header("Location: ../views/shop/profile.php?success=Address added");
}
?>
