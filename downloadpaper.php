<?php
/**
 * Orient Academy, Ujjain - Paper Download Handler
 */
require_once 'includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0){
    header("Location: /PYQ/papers.php");
    exit();
}

// Increment downloads count safely
$stmt = $conn->prepare("UPDATE papers SET downloads = downloads + 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Fetch drive link
$stmt2 = $conn->prepare("SELECT drive_link FROM papers WHERE id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res = $stmt2->get_result();

if($res && $row = $res->fetch_assoc()){
    $link = trim($row['drive_link']);

    // Check if it's a Google Drive link to convert to direct download
    if(preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $link, $matches)){
        $fileId = $matches[1];
        $downloadLink = "https://drive.google.com/uc?export=download&id=" . $fileId;
        header("Location: " . $downloadLink);
        exit();
    }

    if(!empty($link)){
        header("Location: " . $link);
        exit();
    }
}

header("Location: /PYQ/papers.php");
exit();
?>