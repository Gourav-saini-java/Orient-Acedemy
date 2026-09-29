<?php
/**
 * Orient Academy, Ujjain - Admin: Add Course
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$success = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon_class  = trim($_POST['icon_class'] ?? 'bi-mortarboard-fill');
    $badge_color = trim($_POST['badge_color'] ?? 'primary');

    if(empty($course_name)){
        $error = "Course name is required.";
    } else {
        // Generate URL-friendly slug
        $course_slug = strtolower($course_name);
        $course_slug = preg_replace('/[^a-z0-9]+/', '-', $course_slug);
        $course_slug = trim($course_slug, '-');

        // Check if course already exists
        $stmt_check = $conn->prepare("SELECT id FROM courses WHERE course_name = ? OR course_slug = ?");
        $stmt_check->bind_param("ss", $course_name, $course_slug);
        $stmt_check->execute();
        $check_res = $stmt_check->get_result();

        if($check_res && $check_res->num_rows > 0){
            $error = "A course with this name or slug already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO courses (course_name, course_slug, description, icon_class, badge_color) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $course_name, $course_slug, $description, $icon_class, $badge_color);

            if($stmt->execute()){
                $success = "Course '{$course_name}' added successfully!";
            } else {
                $error = "Failed to add course due to a database error.";
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<title>Add Course - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Add New Academic Course</h1>
        <p class="lead">Register a new degree course into Orient Academy catalog.</p>
    </div>
</section>

<section class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="managecourses.php" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to Courses
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

                <form method="POST" action="addcourse.php">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Course Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="course_name"
                               class="form-control"
                               placeholder="e.g. MCA, BCA, B.Tech Computer Science"
                               value="<?php echo htmlspecialchars($_POST['course_name'] ?? ''); ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Course Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Brief overview of the program..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Bootstrap Icon Class</label>
                            <select name="icon_class" class="form-select">
                                <option value="bi-mortarboard-fill">Mortarboard / Graduation (bi-mortarboard-fill)</option>
                                <option value="bi-laptop-fill">Laptop / Tech (bi-laptop-fill)</option>
                                <option value="bi-cpu-fill">CPU / Engineering (bi-cpu-fill)</option>
                                <option value="bi-briefcase-fill">Briefcase / MBA (bi-briefcase-fill)</option>
                                <option value="bi-bank2">Bank / Commerce (bi-bank2)</option>
                                <option value="bi-gear-fill">Gear / Technology (bi-gear-fill)</option>
                                <option value="bi-book-fill">Book / General (bi-book-fill)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Card Badge Color Theme</label>
                            <select name="badge_color" class="form-select">
                                <option value="primary">Primary (Blue)</option>
                                <option value="success">Success (Green)</option>
                                <option value="danger">Danger (Red)</option>
                                <option value="warning">Warning (Yellow/Amber)</option>
                                <option value="info">Info (Cyan)</option>
                                <option value="secondary">Secondary (Gray)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Save & Add Course
                    </button>
                    <a href="managecourses.php" class="btn btn-light rounded-pill px-4 py-2 ms-2">
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