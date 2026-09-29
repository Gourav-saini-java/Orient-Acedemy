<?php
/**
 * Orient Academy, Ujjain - Admin: Add Subject
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$success = '';
$error = '';

$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $course_id    = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $subject_name = trim($_POST['subject_name'] ?? '');
    $subject_code = trim($_POST['subject_code'] ?? '');
    $semester     = trim($_POST['semester'] ?? '1');

    if($course_id <= 0 || empty($subject_name) || empty($semester)){
        $error = "Please select a course and enter subject name and semester.";
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (course_id, subject_name, subject_code, semester) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $course_id, $subject_name, $subject_code, $semester);

        if($stmt->execute()){
            $success = "Subject '{$subject_name}' added successfully!";
        } else {
            $error = "Failed to add subject due to a database error.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<title>Add Subject - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Add New Academic Subject</h1>
        <p class="lead">Assign a new subject to a course degree and semester curriculum.</p>
    </div>
</section>

<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="managesubjects.php" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Subjects
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

                <form method="POST" action="addsubject.php">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Parent Degree Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" required>
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

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Subject Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="subject_name"
                               class="form-control"
                               placeholder="e.g. Database Management Systems (DBMS)"
                               value="<?php echo htmlspecialchars($_POST['subject_name'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Subject Code (Optional)</label>
                            <input type="text"
                                   name="subject_code"
                                   class="form-control"
                                   placeholder="e.g. MCA-201, CS-401"
                                   value="<?php echo htmlspecialchars($_POST['subject_code'] ?? ''); ?>">
                        </div>

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
                    </div>

                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Save & Add Subject
                    </button>
                    <a href="managesubjects.php" class="btn btn-light rounded-pill px-4 py-2 ms-2">
                        Cancel
                    </a>

                </form>

            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
