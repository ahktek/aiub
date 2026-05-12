<?php
session_start();
if(isset($_SESSION['user_id']))
{
    if($_SESSION['role'] == 'admin')
    {
        header("Location: views/admin/dashboard.php");
    }
    else
    {
        header("Location: views/shop/profile.php");
    }
}
else
{
    header("Location: views/auth/login.php");
}
exit();
?>
