<?php
/**
 * Orient Academy, Ujjain - Admin: Delete Subject
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id > 0){
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: managesubjects.php");
exit();
?>
