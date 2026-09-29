<?php
/**
 * Orient Academy, Ujjain - AJAX Endpoint: Get Subjects by Course
 */
require_once '../includes/db.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$selected_id = isset($_GET['selected_id']) ? (int)$_GET['selected_id'] : 0;

echo '<option value="">-- Select Subject --</option>';

if($course_id > 0){
    $stmt = $conn->prepare("SELECT id, subject_name, subject_code, semester FROM subjects WHERE course_id = ? ORDER BY semester ASC, subject_name ASC");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()){
        $codeStr = !empty($row['subject_code']) ? " (" . $row['subject_code'] . ")" : "";
        $selected = ($selected_id === (int)$row['id']) ? 'selected' : '';
        echo '<option value="' . (int)$row['id'] . '" ' . $selected . '>';
        echo htmlspecialchars($row['subject_name'] . $codeStr . " - Sem " . $row['semester']);
        echo '</option>';
    }
}
?>