<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "ecommerce";

$conn = new mysqli(
    $host,
    $user,
    $pass,
    $db
);

if($conn->connect_error)
{
    die("Please connect the database" . $conn->connect_error);
}

session_start();

function require_admin()
{
    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')
    {
        Header("Location: ../views/auth/login.php");
        exit();
    }
}
?>
