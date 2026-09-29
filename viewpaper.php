<?php
/**
 * Orient Academy, Ujjain - Paper View Handler
 */
require_once 'includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0){
    header("Location: /PYQ/papers.php");
    exit();
}

// Increment views count safely
$stmt = $conn->prepare("UPDATE papers SET views = views + 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Fetch drive link
$stmt2 = $conn->prepare("SELECT drive_link FROM papers WHERE id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res = $stmt2->get_result();

if($res && $row = $res->fetch_assoc()){
    $link = trim($row['drive_link']);
    if(!empty($link)){
        header("Location: " . $link);
        exit();
    }
}

header("Location: /PYQ/papers.php");
exit();
?>