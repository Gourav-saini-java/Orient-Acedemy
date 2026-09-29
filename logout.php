<?php
/**
 * Orient Academy, Ujjain - Student Logout
 */
session_start();

unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

session_destroy();

header("Location: /PYQ/index.php");
exit();
?>