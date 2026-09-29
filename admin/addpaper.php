<?php
/**
 * Orient Academy, Ujjain - Admin: Add Paper
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$success = "";
$error = "";

$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $course_id   = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $subject_id  = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
    $year        = isset($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
    $semester    = trim($_POST['semester'] ?? '');
    $paper_title = trim($_POST['paper_title'] ?? '');
    $drive_link  = trim($_POST['drive_link'] ?? '');
    $is_latest   = isset($_POST['is_latest']) ? 1 : 0;

    if($course_id <= 0 || $subject_id <= 0 || empty($year) || empty($semester) || empty($paper_title) || empty($drive_link)){
        $error = "All fields marked with an asterisk (*) are required.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO papers (course_id, subject_id, year, semester, paper_title, drive_link, is_latest)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiisssi", $course_id, $subject_id, $year, $semester, $paper_title, $drive_link, $is_latest);

        if($stmt->execute()){
            $success = "Question paper '{$paper_title}' added successfully!";
        } else {
            $error = "Failed to add question paper due to a database error.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<title>Add Question Paper - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Upload Question Paper</h1>
        <p class="lead">Add new Previous Year Question Papers (PYQs) to Orient Academy library.</p>
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

                <form method="POST" action="addpaper.php">

                    <!-- Course -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Course Degree <span class="text-danger">*</span></label>
                        <select name="course_id" id="courseSelector" class="form-select" required>
                            <option value="">-- Select Course --</option>
                            <?php if($courses_res): ?>
                                <?php while($c = mysqli_fetch_assoc($courses_res)): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo (isset($_POST['course_id']) && $_POST['course_id'] == $c['id']) ? 'selected' : ''; ?>>
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
                            <option value="">-- Select Course First --</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Paper Title <span class="text-danger">*</span></label>
                        <input type="text"
                               name="paper_title"
                               class="form-control"
                               placeholder="e.g. MCA 2nd Sem Database Management Systems May 2024"
                               value="<?php echo htmlspecialchars($_POST['paper_title'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Semester -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select" required>
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($_POST['semester']) && $_POST['semester'] == (string)$i) ? 'selected' : ''; ?>>
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
                                   value="<?php echo htmlspecialchars($_POST['year'] ?? date('Y')); ?>"
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
                                   placeholder="https://drive.google.com/file/d/.../view"
                                   value="<?php echo htmlspecialchars($_POST['drive_link'] ?? ''); ?>"
                                   required>
                        </div>
                        <div class="form-text small">Ensure the Google Drive link has sharing permissions set to "Anyone with the link can view".</div>
                    </div>

                    <!-- Is Latest Checkbox -->
                    <div class="form-check mb-4">
                        <input type="checkbox"
                               class="form-check-input"
                               name="is_latest"
                               id="isLatestCheck"
                               value="1"
                               <?php echo (!isset($_POST['course_id']) || isset($_POST['is_latest'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label small fw-semibold" for="isLatestCheck">
                            Mark as "Latest" paper with highlighted badge
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Question Paper
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
// Dynamic subject loading
document.getElementById('courseSelector').addEventListener('change', function(){
    const courseId = this.value;
    const subjectSelector = document.getElementById('subjectSelector');
    
    if(!courseId){
        subjectSelector.innerHTML = '<option value="">-- Select Course First --</option>';
        return;
    }

    subjectSelector.innerHTML = '<option value="">Loading subjects...</option>';

    fetch('getsubjects.php?course_id=' + courseId)
        .then(response => response.text())
        .then(data => {
            subjectSelector.innerHTML = data;
        })
        .catch(err => {
            subjectSelector.innerHTML = '<option value="">Error loading subjects</option>';
        });
});

// Auto-trigger if pre-selected
if(document.getElementById('courseSelector').value){
    const selectedCourse = document.getElementById('courseSelector').value;
    const preSubject = "<?php echo (int)($_POST['subject_id'] ?? 0); ?>";
    fetch('getsubjects.php?course_id=' + selectedCourse + '&selected_id=' + preSubject)
        .then(response => response.text())
        .then(data => {
            document.getElementById('subjectSelector').innerHTML = data;
        });
}
</script>

</body>
</html>