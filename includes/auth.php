<?php
/**
 * User Authentication Guard
 * Redirects to login page if user is not logged in.
 */
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'])){
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: /PYQ/login.php");
    exit();
}
?>