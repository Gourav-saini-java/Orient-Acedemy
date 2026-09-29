<?php
/**
 * Orient Academy, Ujjain - Subjects Directory
 */
require_once 'includes/db.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$course_slug = isset($_GET['course']) ? trim($_GET['course']) : '';

$course = null;

// Fetch course by ID or Slug
if($course_id > 0){
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();
} elseif(!empty($course_slug)){
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_slug = ? OR LOWER(course_name) = ?");
    $stmt->bind_param("ss", $course_slug, $course_slug);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();
    if($course){
        $course_id = (int)$course['id'];
    }
}

// Fetch subjects
if($course_id > 0){
    $stmt = $conn->prepare("
        SELECT 
            s.*,
            c.course_name,
            COUNT(p.id) AS total_papers
        FROM subjects s
        INNER JOIN courses c ON s.course_id = c.id
        LEFT JOIN papers p ON s.id = p.subject_id
        WHERE s.course_id = ?
        GROUP BY s.id
        ORDER BY s.semester ASC, s.subject_name ASC
    ");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $subjects_res = $stmt->get_result();
} else {
    // Show all subjects across all courses
    $subjects_res = mysqli_query($conn, "
        SELECT 
            s.*,
            c.course_name,
            COUNT(p.id) AS total_papers
        FROM subjects s
        INNER JOIN courses c ON s.course_id = c.id
        LEFT JOIN papers p ON s.id = p.subject_id
        GROUP BY s.id
        ORDER BY c.course_name ASC, s.semester ASC, s.subject_name ASC
    ");
}

// Fetch all courses for the filter dropdown
$all_courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY course_name ASC");

$page_title = $course ? htmlspecialchars($course['course_name']) . " Subjects" : "All Academic Subjects";
?>

<?php include 'includes/header.php'; ?>
<title><?php echo $page_title; ?> - Orient Academy, Ujjain</title>

<body>

<?php include 'includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">
            <?php echo $page_title; ?>
        </h1>
        <p class="lead">
            <?php if($course): ?>
                Explore question papers and curriculum for <?php echo htmlspecialchars($course['course_name']); ?>.
            <?php else: ?>
                Browse curriculum subjects and find previous year examination papers.
            <?php endif; ?>
        </p>

        <!-- Course Filter Pills -->
        <div class="d-flex justify-content-center flex-wrap gap-2 mt-4">
            <a href="subjects.php" class="btn btn-sm <?php echo ($course_id == 0) ? 'btn-warning text-dark fw-bold' : 'btn-outline-light'; ?> rounded-pill px-3">
                All Courses
            </a>
            <?php if($all_courses && mysqli_num_rows($all_courses) > 0): ?>
                <?php while($c = mysqli_fetch_assoc($all_courses)): ?>
                    <a href="subjects.php?course_id=<?php echo $c['id']; ?>"
                       class="btn btn-sm <?php echo ($course_id == $c['id']) ? 'btn-warning text-dark fw-bold' : 'btn-outline-light'; ?> rounded-pill px-3">
                        <?php echo htmlspecialchars($c['course_name']); ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Subjects Grid -->
<section class="container py-5">

    <div class="row g-4">
        <?php if($subjects_res && $subjects_res->num_rows > 0): ?>
            <?php while($subject = $subjects_res->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card subject-card h-100">
                        <div class="card-body p-4 text-center d-flex flex-column">

                            <div class="card-icon-header bg-primary-subtle text-primary">
                                <i class="bi bi-book-half"></i>
                            </div>

                            <div class="d-flex justify-content-center gap-2 mb-2">
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <?php echo htmlspecialchars($subject['course_name']); ?>
                                </span>
                                <span class="badge bg-info-subtle text-info">
                                    Semester <?php echo htmlspecialchars($subject['semester']); ?>
                                </span>
                            </div>

                            <h5 class="fw-bold mb-1 mt-1">
                                <?php echo htmlspecialchars($subject['subject_name']); ?>
                            </h5>

                            <?php if(!empty($subject['subject_code'])): ?>
                                <p class="text-muted small mb-2">
                                    Code: <code><?php echo htmlspecialchars($subject['subject_code']); ?></code>
                                </p>
                            <?php else: ?>
                                <div class="mb-2"></div>
                            <?php endif; ?>

                            <div class="mt-auto pt-3">
                                <a href="papers.php?course_id=<?php echo (int)$subject['course_id']; ?>&subject_id=<?php echo (int)$subject['id']; ?>"
                                   class="btn btn-primary rounded-pill w-100 fw-semibold">
                                    <i class="bi bi-file-earmark-text me-1"></i>
                                    View Papers (<?php echo (int)$subject['total_papers']; ?>)
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x display-1 text-secondary"></i>
                <h3 class="mt-3">No Subjects Found</h3>
                <p class="text-muted">There are no subjects listed under this course yet.</p>
                <a href="courses.php" class="btn btn-outline-primary rounded-pill px-4 mt-2">
                    Browse Other Courses
                </a>
            </div>
        <?php endif; ?>
    </div>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>