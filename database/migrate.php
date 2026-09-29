<?php
/**
 * Database Schema Sync Script
 */
$conn = @mysqli_connect('localhost', 'root', '', 'pyqhunt');
if(!$conn){
    echo "Could not connect: " . mysqli_connect_error() . "\n";
    exit(1);
}

// 1. Ensure subjects has subject_code and semester
$check_sub_code = mysqli_query($conn, "SHOW COLUMNS FROM subjects LIKE 'subject_code'");
if($check_sub_code && mysqli_num_rows($check_sub_code) == 0){
    mysqli_query($conn, "ALTER TABLE subjects ADD COLUMN subject_code VARCHAR(50) NULL AFTER subject_name");
    echo "Added subject_code column to subjects.\n";
}

$check_sub_sem = mysqli_query($conn, "SHOW COLUMNS FROM subjects LIKE 'semester'");
if($check_sub_sem && mysqli_num_rows($check_sub_sem) == 0){
    mysqli_query($conn, "ALTER TABLE subjects ADD COLUMN semester VARCHAR(20) DEFAULT '1' AFTER subject_name");
    echo "Added semester column to subjects.\n";
}

// 2. Ensure courses has icon_class and badge_color
$check_c_icon = mysqli_query($conn, "SHOW COLUMNS FROM courses LIKE 'icon_class'");
if($check_c_icon && mysqli_num_rows($check_c_icon) == 0){
    mysqli_query($conn, "ALTER TABLE courses ADD COLUMN icon_class VARCHAR(50) DEFAULT 'bi-mortarboard-fill'");
    echo "Added icon_class column to courses.\n";
}

$check_c_badge = mysqli_query($conn, "SHOW COLUMNS FROM courses LIKE 'badge_color'");
if($check_c_badge && mysqli_num_rows($check_c_badge) == 0){
    mysqli_query($conn, "ALTER TABLE courses ADD COLUMN badge_color VARCHAR(30) DEFAULT 'primary'");
    echo "Added badge_color column to courses.\n";
}

// 3. Ensure admins has admin account
$check_admin = mysqli_query($conn, "SELECT id FROM admins WHERE username = 'admin' OR username = 'gourav'");
if($check_admin && mysqli_num_rows($check_admin) == 0){
    $hashed = password_hash('1212', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO admins (username, password) VALUES ('gourav', '$hashed')");
    $hashedAdmin = password_hash('password123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO admins (username, password) VALUES ('admin', '$hashedAdmin')");
    echo "Added default admin accounts.\n";
}

echo "Database synchronization completed successfully!\n";
?>
