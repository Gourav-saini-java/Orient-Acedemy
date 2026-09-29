<?php
/**
 * Orient Academy, Ujjain - Administrator Logout
 */
session_start();

unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);

session_destroy();

header("Location: /PYQ/admin/adminlogin.php");
exit();
?>