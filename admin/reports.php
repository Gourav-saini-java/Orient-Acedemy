<?php
/**
 * Orient Academy, Ujjain - Admin: Reports & Analytics
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

// Aggregations
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;
$totalCourses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'] ?? 0;
$totalSubjects = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM subjects"))['total'] ?? 0;
$totalPapers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM papers"))['total'] ?? 0;
$totalViews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(views),0) AS total FROM papers"))['total'] ?? 0;
$totalDownloads = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(downloads),0) AS total FROM papers"))['total'] ?? 0;

// Latest 10 papers
$latestPapers = mysqli_query($conn,"
    SELECT p.paper_title, p.year, p.semester, c.course_name, s.subject_name
    FROM papers p
    LEFT JOIN courses c ON p.course_id = c.id
    LEFT JOIN subjects s ON p.subject_id = s.id
    ORDER BY p.upload_date DESC
    LIMIT 10
");

// Top 5 downloads
$topDownloads = mysqli_query($conn,"
    SELECT p.paper_title, p.downloads, c.course_name 
    FROM papers p
    LEFT JOIN courses c ON p.course_id = c.id
    ORDER BY p.downloads DESC 
    LIMIT 5
");

// Top 5 views
$topViews = mysqli_query($conn,"
    SELECT p.paper_title, p.views, c.course_name 
    FROM papers p
    LEFT JOIN courses c ON p.course_id = c.id
    ORDER BY p.views DESC 
    LIMIT 5
");

// Course-wise Paper counts for Chart.js
$courseStats = mysqli_query($conn,"
    SELECT c.course_name, COUNT(p.id) AS total_papers
    FROM courses c
    LEFT JOIN papers p ON c.id = p.course_id
    GROUP BY c.id
    ORDER BY total_papers DESC
");

$courseNames = [];
$paperCounts = [];

if($courseStats){
    while ($row = mysqli_fetch_assoc($courseStats)) {
        $courseNames[] = $row['course_name'];
        $paperCounts[] = (int)$row['total_papers'];
    }
}
?>

<?php include '../includes/header.php'; ?>
<title>Reports & Analytics - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Hero -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Platform Analytics & Reports</h1>
        <p class="lead">Monitor paper engagement, student activity, and course distribution metrics.</p>
    </div>
</section>

<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">Platform Overview</h3>
        <a href="admindashboard.php" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">

        <div class="col-lg-4 col-md-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalUsers); ?></div>
                <p class="stats-label">Registered Students</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-danger-subtle text-danger">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalCourses); ?></div>
                <p class="stats-label">Total Courses</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-info-subtle text-info">
                    <i class="bi bi-book-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalSubjects); ?></div>
                <p class="stats-label">Total Subjects</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-purple-subtle text-purple" style="background:#ede9fe;">
                    <i class="bi bi-file-earmark-pdf-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalPapers); ?></div>
                <p class="stats-label">Question Papers</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-secondary-subtle text-secondary">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalViews); ?></div>
                <p class="stats-label">Paper Views</p>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="stats-card-modern">
                <div class="stats-icon-wrap bg-success-subtle text-success">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                </div>
                <div class="stats-number"><?php echo number_format($totalDownloads); ?></div>
                <p class="stats-label">Total Downloads</p>
            </div>
        </div>

    </div>

    <!-- Chart: Course-wise distribution -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 bg-white p-4">
        <h4 class="fw-bold mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-bar-chart-fill text-primary"></i> Question Papers Distribution by Course
        </h4>
        <div style="max-height: 380px;">
            <canvas id="papersChart"></canvas>
        </div>
    </div>

    <!-- Top Downloads & Top Views Section -->
    <div class="row g-4 mb-5">

        <!-- Top Downloads -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-download text-success"></i> Most Downloaded Papers
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Paper Title</th>
                                <th>Course</th>
                                <th class="text-end">Downloads</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($topDownloads && mysqli_num_rows($topDownloads) > 0): ?>
                                <?php while($td = mysqli_fetch_assoc($topDownloads)): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($td['paper_title']); ?></strong></td>
                                        <td><span class="badge bg-primary-subtle text-primary"><?php echo htmlspecialchars($td['course_name'] ?? '-'); ?></span></td>
                                        <td class="text-end"><span class="badge bg-success-subtle text-success px-3 py-1 fw-bold"><?php echo (int)$td['downloads']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">No download data available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Views -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white p-4">
                <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-eye-fill text-primary"></i> Most Viewed Papers
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Paper Title</th>
                                <th>Course</th>
                                <th class="text-end">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($topViews && mysqli_num_rows($topViews) > 0): ?>
                                <?php while($tv = mysqli_fetch_assoc($topViews)): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($tv['paper_title']); ?></strong></td>
                                        <td><span class="badge bg-primary-subtle text-primary"><?php echo htmlspecialchars($tv['course_name'] ?? '-'); ?></span></td>
                                        <td class="text-end"><span class="badge bg-primary-subtle text-primary px-3 py-1 fw-bold"><?php echo (int)$tv['views']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">No views recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('papersChart');
if(ctx){
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($courseNames); ?>,
            datasets: [{
                label: 'Available Question Papers',
                data: <?php echo json_encode($paperCounts); ?>,
                backgroundColor: [
                    'rgba(37, 99, 235, 0.85)',
                    'rgba(16, 185, 129, 0.85)',
                    'rgba(239, 68, 68, 0.85)',
                    'rgba(245, 158, 11, 0.85)',
                    'rgba(6, 182, 212, 0.85)',
                    'rgba(139, 92, 246, 0.85)'
                ],
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
}
</script>

</body>
</html>
