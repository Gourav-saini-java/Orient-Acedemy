<?php
/**
 * Orient Academy, Ujjain - Home Page
 */
require_once 'includes/db.php';

// Fetch live statistics
$stats_papers = 0;
$stats_subjects = 0;
$stats_courses = 0;
$stats_downloads = 0;

$p_res = mysqli_query($conn, "SELECT COUNT(*) AS total, COALESCE(SUM(downloads), 0) AS downloads FROM papers");
if($p_res && $row = mysqli_fetch_assoc($p_res)){
    $stats_papers = (int)$row['total'];
    $stats_downloads = (int)$row['downloads'];
}

$s_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM subjects");
if($s_res && $row = mysqli_fetch_assoc($s_res)){
    $stats_subjects = (int)$row['total'];
}

$c_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses");
if($c_res && $row = mysqli_fetch_assoc($c_res)){
    $stats_courses = (int)$row['total'];
}

// Fetch popular courses with paper counts
$courses_sql = "
SELECT 
    c.*, 
    COUNT(DISTINCT s.id) AS total_subjects,
    COUNT(DISTINCT p.id) AS total_papers
FROM courses c
LEFT JOIN subjects s ON c.id = s.course_id
LEFT JOIN papers p ON c.id = p.course_id
GROUP BY c.id
ORDER BY total_papers DESC, c.course_name ASC
LIMIT 6
";
$courses_res = mysqli_query($conn, $courses_sql);

// Fetch latest uploaded papers
$latest_papers_sql = "
SELECT 
    p.*,
    c.course_name,
    s.subject_name,
    s.subject_code
FROM papers p
INNER JOIN courses c ON p.course_id = c.id
INNER JOIN subjects s ON p.subject_id = s.id
ORDER BY p.upload_date DESC, p.id DESC
LIMIT 8
";
$latest_papers_res = mysqli_query($conn, $latest_papers_sql);
?>

<?php include 'includes/header.php'; ?>
<title>Orient Academy, Ujjain - Previous Year Question Papers & Study Materials</title>

<body>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero text-center">
    <div class="container position-relative">

        <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-25 px-3 py-1 rounded-pill mb-3">
            <i class="bi bi-mortarboard-fill text-warning"></i>
            <span class="small fw-semibold">Premier Academic Resource Center &bull; Ujjain</span>
        </div>

        <h1 class="mb-3">Orient Academy, Ujjain</h1>

        <p class="lead mb-4">
            Free access to verified Previous Year Question Papers (PYQ), syllabus, and exam reference materials for MCA, BCA, B.Tech, MBA, and more.
        </p>

        <!-- Search Bar -->
        <div class="search-box-wrap">
            <form action="search.php" method="GET">
                <div class="input-group search-input-group">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Search course, subject name, subject code, or year..."
                           required>
                    <button class="btn btn-warning text-dark fw-bold px-4" type="submit">
                        <i class="bi bi-search me-1"></i> Search Papers
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>

<!-- Dynamic Statistics Counter -->
<section class="container my-5">
    <div class="row g-4">

        <div class="col-6 col-md-3">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($stats_papers); ?>+</div>
                <p class="stats-label">Question Papers</p>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-info-subtle text-info">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($stats_subjects); ?>+</div>
                <p class="stats-label">Academic Subjects</p>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-danger-subtle text-danger">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($stats_courses); ?>+</div>
                <p class="stats-label">Degree Courses</p>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-success-subtle text-success">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($stats_downloads); ?>+</div>
                <p class="stats-label">Total Downloads</p>
            </div>
        </div>

    </div>
</section>

<!-- Popular / Available Courses -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Academic Programs</h2>
            <p class="text-muted mb-0">Select your degree to browse subjects and past question papers</p>
        </div>
        <a href="courses.php" class="btn btn-outline-primary rounded-pill px-4">
            View All Courses <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        <?php if($courses_res && mysqli_num_rows($courses_res) > 0): ?>
            <?php while($course = mysqli_fetch_assoc($courses_res)): 
                $icon = !empty($course['icon_class']) ? $course['icon_class'] : 'bi-mortarboard-fill';
                $badgeColor = !empty($course['badge_color']) ? $course['badge_color'] : 'primary';
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card course-card">
                        <div class="card-body text-center p-4 d-flex flex-column">
                            <div class="card-icon-header bg-<?php echo htmlspecialchars($badgeColor); ?>-subtle text-<?php echo htmlspecialchars($badgeColor); ?>">
                                <i class="bi <?php echo htmlspecialchars($icon); ?>"></i>
                            </div>

                            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($course['course_name']); ?></h4>
                            <p class="text-muted small flex-grow-1 mb-3">
                                <?php echo htmlspecialchars($course['description'] ?? 'Comprehensive past question papers archive.'); ?>
                            </p>

                            <div class="d-flex justify-content-center gap-2 mb-3">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-book me-1"></i> <?php echo (int)$course['total_subjects']; ?> Subjects
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-file-text me-1"></i> <?php echo (int)$course['total_papers']; ?> Papers
                                </span>
                            </div>

                            <a href="subjects.php?course_id=<?php echo $course['id']; ?>" class="btn btn-primary rounded-pill w-100 fw-semibold">
                                View Subjects <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4">
                <p class="text-muted">No courses found. Please import the database schema or add courses via the admin panel.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Latest Uploaded Papers -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">Latest Uploaded Papers</h2>
            <p class="text-muted mb-0">Recently added examination question papers</p>
        </div>
        <a href="papers.php" class="btn btn-outline-success rounded-pill px-4">
            Browse All Papers <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Subject</th>
                        <th>Paper Title</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($latest_papers_res && mysqli_num_rows($latest_papers_res) > 0): ?>
                        <?php while($paper = mysqli_fetch_assoc($latest_papers_res)): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-semibold">
                                        <?php echo htmlspecialchars($paper['course_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($paper['subject_name']); ?></strong>
                                    <?php if(!empty($paper['subject_code'])): ?>
                                        <div class="small text-muted"><?php echo htmlspecialchars($paper['subject_code']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-medium"><?php echo htmlspecialchars($paper['paper_title']); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        Sem <?php echo htmlspecialchars($paper['semester']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?php echo htmlspecialchars($paper['year']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="viewpaper.php?id=<?php echo (int)$paper['id']; ?>" target="_blank" class="btn btn-outline-primary" title="View Paper Online">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="downloadpaper.php?id=<?php echo (int)$paper['id']; ?>" class="btn btn-success" title="Download Paper">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No question papers uploaded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Why Choose Orient Academy -->
<section class="bg-white py-5 border-top border-bottom my-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Orient Academy, Ujjain?</h2>
            <p class="text-muted">Empowering students with seamless exam preparation resources</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4">
                    <div class="stats-icon-wrap bg-primary-subtle text-primary mx-auto mb-3" style="width:70px; height:70px; font-size:2rem;">
                        <i class="bi bi-cloud-arrow-down-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Direct Fast Downloads</h4>
                    <p class="text-muted">High-speed direct access to Google Drive papers with single-click download counters.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4">
                    <div class="stats-icon-wrap bg-warning-subtle text-warning mx-auto mb-3" style="width:70px; height:70px; font-size:2rem;">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Multi-Filter Search</h4>
                    <p class="text-muted">Easily find papers organized by course, subject, semester, and examination year.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4">
                    <div class="stats-icon-wrap bg-success-subtle text-success mx-auto mb-3" style="width:70px; height:70px; font-size:2rem;">
                        <i class="bi bi-phone-fill"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Mobile Optimized</h4>
                    <p class="text-muted">Study anytime, anywhere with a responsive design built for smartphones, tablets, and PCs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</body>
</html>