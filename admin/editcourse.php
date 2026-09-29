<?php
/**
 * Orient Academy, Ujjain - Admin: Edit Course
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){
    header("Location: managecourses.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if(!$course){
    header("Location: managecourses.php");
    exit();
}

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
        $course_slug = strtolower($course_name);
        $course_slug = preg_replace('/[^a-z0-9]+/', '-', $course_slug);
        $course_slug = trim($course_slug, '-');

        // Check duplicate name on another ID
        $stmt_check = $conn->prepare("SELECT id FROM courses WHERE (course_name = ? OR course_slug = ?) AND id != ?");
        $stmt_check->bind_param("ssi", $course_name, $course_slug, $id);
        $stmt_check->execute();
        $check_res = $stmt_check->get_result();

        if($check_res && $check_res->num_rows > 0){
            $error = "Another course with this name or slug already exists.";
        } else {
            $stmt_update = $conn->prepare("UPDATE courses SET course_name = ?, course_slug = ?, description = ?, icon_class = ?, badge_color = ? WHERE id = ?");
            $stmt_update->bind_param("sssssi", $course_name, $course_slug, $description, $icon_class, $badge_color, $id);

            if($stmt_update->execute()){
                $success = "Course updated successfully!";
                $course['course_name'] = $course_name;
                $course['course_slug'] = $course_slug;
                $course['description'] = $description;
                $course['icon_class']  = $icon_class;
                $course['badge_color'] = $badge_color;
            } else {
                $error = "Failed to update course due to a database error.";
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<title>Edit Course: <?php echo htmlspecialchars($course['course_name']); ?> - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Edit Academic Course</h1>
        <p class="lead">Update details for <?php echo htmlspecialchars($course['course_name']); ?></p>
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

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Course Name <span class="text-danger">*</span></label>
                        <input type="text"
                               name="course_name"
                               class="form-control"
                               value="<?php echo htmlspecialchars($course['course_name']); ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Course Description</label>
                        <textarea name="description"
                                  class="form-control"
                                  rows="3"><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Bootstrap Icon Class</label>
                            <select name="icon_class" class="form-select">
                                <?php
                                $icons = [
                                    'bi-mortarboard-fill' => 'Mortarboard / Graduation (bi-mortarboard-fill)',
                                    'bi-laptop-fill' => 'Laptop / Tech (bi-laptop-fill)',
                                    'bi-cpu-fill' => 'CPU / Engineering (bi-cpu-fill)',
                                    'bi-briefcase-fill' => 'Briefcase / MBA (bi-briefcase-fill)',
                                    'bi-bank2' => 'Bank / Commerce (bi-bank2)',
                                    'bi-gear-fill' => 'Gear / Technology (bi-gear-fill)',
                                    'bi-book-fill' => 'Book / General (bi-book-fill)'
                                ];
                                foreach($icons as $val => $label):
                                ?>
                                    <option value="<?php echo $val; ?>" <?php echo ($course['icon_class'] == $val) ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Badge Color Theme</label>
                            <select name="badge_color" class="form-select">
                                <?php
                                $colors = ['primary', 'success', 'danger', 'warning', 'info', 'secondary'];
                                foreach($colors as $col):
                                ?>
                                    <option value="<?php echo $col; ?>" <?php echo (($course['badge_color'] ?? 'primary') == $col) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($col); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Update Course
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
