<?php
if(!isset($_SESSION))
{
    session_start();
}

function require_admin()
{
    if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin')
    {
        Header("Location: ../views/auth/login.php");
        exit();
    }
}
?>
