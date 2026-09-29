<?php
/**
 * Orient Academy, Ujjain - Academic Courses Directory
 */
require_once 'includes/db.php';

// Fetch all courses with live counts of subjects and papers
$sql = "
SELECT 
    c.*, 
    COUNT(DISTINCT s.id) AS total_subjects,
    COUNT(DISTINCT p.id) AS total_papers
FROM courses c
LEFT JOIN subjects s ON c.id = s.course_id
LEFT JOIN papers p ON c.id = p.course_id
GROUP BY c.id
ORDER BY c.course_name ASC
";
$result = mysqli_query($conn, $sql);
?>

<?php include 'includes/header.php'; ?>
<title>Academic Courses - Orient Academy, Ujjain</title>

<body>

<?php include 'includes/navbar.php'; ?>

<!-- Page Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Academic Programs & Courses</h1>
        <p class="lead">Explore past year question papers and syllabus across all departments.</p>
    </div>
</section>

<!-- Courses Section -->
<section class="container py-5">

    <div class="row g-4">
        <?php if($result && mysqli_num_rows($result) > 0): ?>
            <?php while($course = mysqli_fetch_assoc($result)): 
                $icon = !empty($course['icon_class']) ? $course['icon_class'] : 'bi-mortarboard-fill';
                $badgeColor = !empty($course['badge_color']) ? $course['badge_color'] : 'primary';
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card course-card h-100">
                        <div class="card-body text-center p-4 d-flex flex-column">

                            <div class="card-icon-header bg-<?php echo htmlspecialchars($badgeColor); ?>-subtle text-<?php echo htmlspecialchars($badgeColor); ?>">
                                <i class="bi <?php echo htmlspecialchars($icon); ?>"></i>
                            </div>

                            <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($course['course_name']); ?></h3>
                            <p class="text-muted small flex-grow-1 mb-3">
                                <?php echo htmlspecialchars($course['description'] ?? 'Degree program past question papers archive.'); ?>
                            </p>

                            <div class="d-flex justify-content-center gap-2 mb-4">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-book me-1"></i> <?php echo (int)$course['total_subjects']; ?> Subjects
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> <?php echo (int)$course['total_papers']; ?> Papers
                                </span>
                            </div>

                            <a href="subjects.php?course_id=<?php echo $course['id']; ?>" class="btn btn-<?php echo htmlspecialchars($badgeColor); ?> rounded-pill w-100 fw-semibold">
                                View Subjects & Papers <i class="bi bi-chevron-right ms-1"></i>
                            </a>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox display-1 text-secondary"></i>
                <h3 class="mt-3">No Courses Found</h3>
                <p class="text-muted">Courses will appear here once added in the database or admin dashboard.</p>
            </div>
        <?php endif; ?>
    </div>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>