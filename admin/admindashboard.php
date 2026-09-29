<?php
/**
 * Orient Academy, Ujjain - Administrator Dashboard
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

// Fetch key platform statistics
$userCount = 0;
$paperCount = 0;
$courseCount = 0;
$subjectCount = 0;
$totalViews = 0;
$totalDownloads = 0;

$uQuery = $conn->query("SELECT COUNT(*) AS total FROM users");
if($uQuery && $row = $uQuery->fetch_assoc()) $userCount = (int)$row['total'];

$pQuery = $conn->query("SELECT COUNT(*) AS total, COALESCE(SUM(views),0) AS views, COALESCE(SUM(downloads),0) AS downloads FROM papers");
if($pQuery && $row = $pQuery->fetch_assoc()){
    $paperCount = (int)$row['total'];
    $totalViews = (int)$row['views'];
    $totalDownloads = (int)$row['downloads'];
}

$cQuery = $conn->query("SELECT COUNT(*) AS total FROM courses");
if($cQuery && $row = $cQuery->fetch_assoc()) $courseCount = (int)$row['total'];

$sQuery = $conn->query("SELECT COUNT(*) AS total FROM subjects");
if($sQuery && $row = $sQuery->fetch_assoc()) $subjectCount = (int)$row['total'];

// Fetch recent 5 papers
$recentPapers = $conn->query("
    SELECT p.*, c.course_name, s.subject_name 
    FROM papers p 
    LEFT JOIN courses c ON p.course_id = c.id 
    LEFT JOIN subjects s ON p.subject_id = s.id 
    ORDER BY p.id DESC LIMIT 5
");
?>

<?php include '../includes/header.php'; ?>
<title>Admin Dashboard - Orient Academy, Ujjain</title>

<body>
<?php include '../includes/navbar.php'; ?>

<!-- Hero / Greeting -->
<section class="hero text-center py-5">
    <div class="container">
        <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-25 px-3 py-1 rounded-pill mb-3">
            <i class="bi bi-shield-check text-warning"></i>
            <span class="small fw-semibold">Administration & Management Console</span>
        </div>
        <h1 class="fw-bold mb-2">Orient Academy Dashboard</h1>
        <p class="lead">
            Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?></strong>. Manage curriculum, papers, and platform analytics.
        </p>
    </div>
</section>

<!-- KPI Metrics Overview -->
<section class="container my-5">
    <div class="row g-4">

        <div class="col-md-4 col-sm-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-primary-subtle text-primary">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($paperCount); ?></div>
                <p class="stats-label">Total Question Papers</p>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-danger-subtle text-danger">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($courseCount); ?></div>
                <p class="stats-label">Degree Courses</p>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-info-subtle text-info">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($subjectCount); ?></div>
                <p class="stats-label">Academic Subjects</p>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-warning-subtle text-warning">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($userCount); ?></div>
                <p class="stats-label">Registered Students</p>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-success-subtle text-success">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalDownloads); ?></div>
                <p class="stats-label">Total Paper Downloads</p>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-secondary-subtle text-secondary">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalViews); ?></div>
                <p class="stats-label">Total Paper Views</p>
            </div>
        </div>

    </div>
</section>

<!-- Quick Action Management Hub -->
<section class="container my-5">
    <h3 class="fw-bold mb-4">Management Hub</h3>

    <div class="row g-4">

        <!-- Add Paper -->
        <div class="col-lg-4 col-md-6">
            <div class="card course-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="card-icon-header bg-primary-subtle text-primary">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Upload Question Paper</h5>
                    <p class="text-muted small flex-grow-1">Add new previous year examination paper links.</p>
                    <a href="addpaper.php" class="btn btn-primary rounded-pill w-100 fw-semibold">
                        <i class="bi bi-plus-circle me-1"></i> Add Paper
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Papers -->
        <div class="col-lg-4 col-md-6">
            <div class="card course-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="card-icon-header bg-success-subtle text-success">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Manage Papers</h5>
                    <p class="text-muted small flex-grow-1">View, edit details, or delete uploaded question papers.</p>
                    <a href="managepapers.php" class="btn btn-success rounded-pill w-100 fw-semibold">
                        Manage Papers (<?php echo $paperCount; ?>)
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Courses -->
        <div class="col-lg-4 col-md-6">
            <div class="card course-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="card-icon-header bg-danger-subtle text-danger">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Manage Courses</h5>
                    <p class="text-muted small flex-grow-1">Add, update degree programs and course codes.</p>
                    <a href="managecourses.php" class="btn btn-danger rounded-pill w-100 fw-semibold">
                        Manage Courses (<?php echo $courseCount; ?>)
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Subjects -->
        <div class="col-lg-4 col-md-6">
            <div class="card course-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="card-icon-header bg-info-subtle text-info">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Manage Subjects</h5>
                    <p class="text-muted small flex-grow-1">Organize curriculum subjects across courses & semesters.</p>
                    <a href="managesubjects.php" class="btn btn-info text-white rounded-pill w-100 fw-semibold">
                        Manage Subjects (<?php echo $subjectCount; ?>)
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Users -->
        <div class="col-lg-4 col-md-6">
            <div class="card course-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="card-icon-header bg-warning-subtle text-warning">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Registered Students</h5>
                    <p class="text-muted small flex-grow-1">Monitor registered student accounts and activity.</p>
                    <a href="manageusers.php" class="btn btn-warning text-dark rounded-pill w-100 fw-semibold">
                        Manage Users (<?php echo $userCount; ?>)
                    </a>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-lg-4 col-md-6">
            <div class="card course-card">
                <div class="card-body text-center p-4 d-flex flex-column">
                    <div class="card-icon-header bg-purple-subtle text-purple" style="background:#ede9fe;">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Reports & Analytics</h5>
                    <p class="text-muted small flex-grow-1">Interactive charts for downloads, views and popularity.</p>
                    <a href="reports.php" class="btn btn-dark rounded-pill w-100 fw-semibold">
                        View Analytics <i class="bi bi-graph-up ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Recently Added Papers Table -->
<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Recently Added Question Papers</h4>
        <a href="managepapers.php" class="btn btn-outline-primary btn-sm rounded-pill">View All Papers</a>
    </div>

    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Subject</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>Views / Downloads</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($recentPapers && $recentPapers->num_rows > 0): ?>
                        <?php while($p = $recentPapers->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($p['paper_title']); ?></td>
                                <td><span class="badge bg-primary-subtle text-primary"><?php echo htmlspecialchars($p['course_name'] ?? '-'); ?></span></td>
                                <td><?php echo htmlspecialchars($p['subject_name'] ?? '-'); ?></td>
                                <td>Sem <?php echo htmlspecialchars($p['semester']); ?></td>
                                <td><?php echo htmlspecialchars($p['year']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        👁 <?php echo (int)$p['views']; ?> | 📥 <?php echo (int)$p['downloads']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="editpaper.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-outline-warning" title="Edit Paper">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="deletepaper.php?id=<?php echo (int)$p['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Delete this paper?');" title="Delete Paper">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No papers added yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

</body>
</html>
