<?php
/**
 * Admin Authentication Guard
 * Redirects to admin login page if admin is not logged in.
 */
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    header("Location: /PYQ/admin/adminlogin.php");
    exit();
}
?>