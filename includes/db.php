<?php
/**
 * Orient Academy, Ujjain
 * Database Connection Module
 */

$host     = "localhost";
$user     = "root";
$password = "";
$database = "pyqhunt";

// Turn off default fatal exception for custom handling if desired
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect(
    $host,
    $user,
    $password,
    $database
);

if(!$conn){
    // If database connection fails, provide a helpful development notice
    $error_msg = mysqli_connect_error();
    die("<div style='font-family:sans-serif; padding:30px; max-width:650px; margin:50px auto; background:#fff3f3; border:1px solid #fca5a5; border-radius:12px; color:#991b1b;'>
        <h2 style='margin-top:0;'>⚠️ Database Connection Error</h2>
        <p>Could not connect to the MySQL database (<strong>{$database}</strong>).</p>
        <p style='background:#fee2e2; padding:10px; border-radius:6px; font-family:monospace;'>{$error_msg}</p>
        <h4>Quick Fix Steps:</h4>
        <ol>
            <li>Make sure <strong>Apache</strong> and <strong>MySQL</strong> are started in your <strong>XAMPP Control Panel</strong>.</li>
            <li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a> and create a database named <code>pyqhunt</code> (or import <code>database/schema.sql</code>).</li>
        </ol>
    </div>");
}

mysqli_set_charset($conn, "utf8mb4");
?>