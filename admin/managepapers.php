<?php
/**
 * Orient Academy, Ujjain - Admin: Manage Papers
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course_id = isset($_GET['course_id']) && is_numeric($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$subject_id = isset($_GET['subject_id']) && is_numeric($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

$sql = "
SELECT 
    p.*,
    c.course_name,
    s.subject_name,
    s.subject_code
FROM papers p
INNER JOIN courses c ON p.course_id = c.id
INNER JOIN subjects s ON p.subject_id = s.id
WHERE 1=1
";

if(!empty($search)){
    $safe = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (p.paper_title LIKE '%$safe%' OR s.subject_name LIKE '%$safe%' OR s.subject_code LIKE '%$safe%')";
}

if($course_id > 0){
    $sql .= " AND p.course_id = $course_id";
}

if($subject_id > 0){
    $sql .= " AND p.subject_id = $subject_id";
}

$sql .= " ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);

$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");

$totalPapersCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM papers"))['total'] ?? 0;
?>

<?php include '../includes/header.php'; ?>
<title>Manage Papers - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Manage Question Papers</h1>
        <p class="lead">View, update, or remove question papers from the public repository.</p>
    </div>
</section>

<section class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                Question Papers Repository
                <span class="badge bg-success fs-6 ms-2 align-middle"><?php echo (int)$totalPapersCount; ?> Total</span>
            </h3>
        </div>

        <div class="d-flex gap-2">
            <a href="addpaper.php" class="btn btn-primary rounded-pill px-4 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Add New Paper
            </a>
            <a href="admindashboard.php" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form method="GET" action="managepapers.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search paper title, subject..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    <?php if($courses_res): ?>
                        <?php while($c = mysqli_fetch_assoc($courses_res)): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($course_id == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['course_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-semibold rounded-3">
                    Filter
                </button>
            </div>
            <div class="col-md-2 text-end">
                <?php if(!empty($search) || $course_id > 0 || $subject_id > 0): ?>
                    <a href="managepapers.php" class="btn btn-outline-secondary w-100 rounded-3">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Paper Title</th>
                        <th>Course</th>
                        <th>Subject</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>Views / Downloads</th>
                        <th class="text-center" style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($paper = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo $paper['id']; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($paper['paper_title']); ?></strong>
                                    <?php if(!empty($paper['is_latest'])): ?>
                                        <span class="badge bg-success-subtle text-success ms-1 small">Latest</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                        <?php echo htmlspecialchars($paper['course_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($paper['subject_name']); ?>
                                    <?php if(!empty($paper['subject_code'])): ?>
                                        <div class="small text-muted"><code><?php echo htmlspecialchars($paper['subject_code']); ?></code></div>
                                    <?php endif; ?>
                                </td>
                                <td>Sem <?php echo htmlspecialchars($paper['semester']); ?></td>
                                <td><?php echo htmlspecialchars($paper['year']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        👁 <?php echo (int)$paper['views']; ?> &bull; 📥 <?php echo (int)$paper['downloads']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo htmlspecialchars($paper['drive_link']); ?>"
                                           target="_blank"
                                           class="btn btn-outline-primary"
                                           title="Preview Link">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <a href="editpaper.php?id=<?php echo $paper['id']; ?>"
                                           class="btn btn-outline-warning"
                                           title="Edit Paper">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="deletepaper.php?id=<?php echo $paper['id']; ?>"
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Delete this question paper permanently?');"
                                           title="Delete Paper">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No question papers found.
                            </td>
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