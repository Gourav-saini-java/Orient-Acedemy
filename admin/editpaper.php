<?php
/**
 * Orient Academy, Ujjain - Admin: Edit Paper
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){
    header("Location: managepapers.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM papers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$paper = $stmt->get_result()->fetch_assoc();

if(!$paper){
    header("Location: managepapers.php");
    exit();
}

$success = "";
$error = "";

$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $course_id   = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $subject_id  = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
    $year        = isset($_POST['year']) ? (int)$_POST['year'] : 0;
    $semester    = trim($_POST['semester'] ?? '');
    $paper_title = trim($_POST['paper_title'] ?? '');
    $drive_link  = trim($_POST['drive_link'] ?? '');
    $is_latest   = isset($_POST['is_latest']) ? 1 : 0;

    if($course_id <= 0 || $subject_id <= 0 || empty($year) || empty($semester) || empty($paper_title) || empty($drive_link)){
        $error = "All fields marked with an asterisk (*) are required.";
    } else {
        $stmt_update = $conn->prepare("
            UPDATE papers 
            SET course_id = ?, subject_id = ?, year = ?, semester = ?, paper_title = ?, drive_link = ?, is_latest = ?
            WHERE id = ?
        ");
        $stmt_update->bind_param("iiisssii", $course_id, $subject_id, $year, $semester, $paper_title, $drive_link, $is_latest, $id);

        if($stmt_update->execute()){
            $success = "Question paper updated successfully!";
            $paper['course_id']   = $course_id;
            $paper['subject_id']  = $subject_id;
            $paper['year']        = $year;
            $paper['semester']    = $semester;
            $paper['paper_title'] = $paper_title;
            $paper['drive_link']  = $drive_link;
            $paper['is_latest']   = $is_latest;
        } else {
            $error = "Failed to update question paper due to a database error.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<title>Edit Paper: <?php echo htmlspecialchars($paper['paper_title']); ?> - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Edit Question Paper</h1>
        <p class="lead">Update examination paper details, semester, and links.</p>
    </div>
</section>

<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="managepapers.php" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Papers
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">

                <?php if(!empty($success)): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <!-- Course -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Course Degree <span class="text-danger">*</span></label>
                        <select name="course_id" id="courseSelector" class="form-select" required>
                            <option value="">-- Select Course --</option>
                            <?php if($courses_res): ?>
                                <?php while($c = mysqli_fetch_assoc($courses_res)): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo ($paper['course_id'] == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['course_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="subjectSelector" class="form-select" required>
                            <option value="">Loading subjects...</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Paper Title <span class="text-danger">*</span></label>
                        <input type="text"
                               name="paper_title"
                               class="form-control"
                               value="<?php echo htmlspecialchars($paper['paper_title']); ?>"
                               required>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Semester -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select" required>
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($paper['semester'] == (string)$i) ? 'selected' : ''; ?>>
                                        Semester <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Year -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Examination Year <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="year"
                                   class="form-control"
                                   min="2000"
                                   max="2099"
                                   value="<?php echo htmlspecialchars($paper['year']); ?>"
                                   required>
                        </div>
                    </div>

                    <!-- Google Drive / PDF Link -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Google Drive / Cloud PDF URL <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                            <input type="url"
                                   name="drive_link"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($paper['drive_link']); ?>"
                                   required>
                        </div>
                    </div>

                    <!-- Is Latest Checkbox -->
                    <div class="form-check mb-4">
                        <input type="checkbox"
                               class="form-check-input"
                               name="is_latest"
                               id="isLatestCheck"
                               value="1"
                               <?php echo !empty($paper['is_latest']) ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-semibold" for="isLatestCheck">
                            Mark as "Latest" paper with highlighted badge
                        </label>
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Update Paper Details
                    </button>
                    <a href="managepapers.php" class="btn btn-light rounded-pill px-4 py-2 ms-2">
                        Cancel
                    </a>

                </form>

            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
function loadSubjects(courseId, selectedSubjectId){
    const subjectSelector = document.getElementById('subjectSelector');
    if(!courseId){
        subjectSelector.innerHTML = '<option value="">-- Select Course First --</option>';
        return;
    }
    fetch('getsubjects.php?course_id=' + courseId + '&selected_id=' + selectedSubjectId)
        .then(response => response.text())
        .then(data => {
            subjectSelector.innerHTML = data;
        })
        .catch(err => {
            subjectSelector.innerHTML = '<option value="">Error loading subjects</option>';
        });
}

document.getElementById('courseSelector').addEventListener('change', function(){
    loadSubjects(this.value, 0);
});

// Initial load with current values
loadSubjects("<?php echo (int)$paper['course_id']; ?>", "<?php echo (int)$paper['subject_id']; ?>");
</script>

</body>
</html>