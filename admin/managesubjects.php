<?php
/**
 * Orient Academy, Ujjain - Admin: Manage Subjects
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course_id = isset($_GET['course_id']) && is_numeric($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$sql = "
SELECT 
    s.*,
    c.course_name,
    COUNT(p.id) AS total_papers
FROM subjects s
INNER JOIN courses c ON s.course_id = c.id
LEFT JOIN papers p ON s.id = p.subject_id
WHERE 1=1
";

if(!empty($search)){
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (s.subject_name LIKE '%$safeSearch%' OR s.subject_code LIKE '%$safeSearch%')";
}

if($course_id > 0){
    $sql .= " AND s.course_id = $course_id";
}

$sql .= " GROUP BY s.id ORDER BY c.course_name ASC, s.semester ASC, s.subject_name ASC";

$result = mysqli_query($conn, $sql);

$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");

$totalSubjectsCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM subjects"))['total'] ?? 0;
?>

<?php include '../includes/header.php'; ?>
<title>Manage Subjects - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Manage Academic Subjects</h1>
        <p class="lead">Add, edit, or configure curriculum subjects and codes for all degrees.</p>
    </div>
</section>

<section class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                Subjects Directory
                <span class="badge bg-info fs-6 ms-2 align-middle"><?php echo (int)$totalSubjectsCount; ?> Total</span>
            </h3>
        </div>

        <div class="d-flex gap-2">
            <a href="addsubject.php" class="btn btn-info text-white rounded-pill px-4 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Add New Subject
            </a>
            <a href="admindashboard.php" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form method="GET" action="managesubjects.php" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by subject name or code..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-4">
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
        </form>
    </div>

    <!-- Subjects Table -->
    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Subject Name</th>
                        <th>Subject Code</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th class="text-center">Papers</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($sub = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo $sub['id']; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($sub['subject_name']); ?></strong>
                                </td>
                                <td>
                                    <?php if(!empty($sub['subject_code'])): ?>
                                        <code><?php echo htmlspecialchars($sub['subject_code']); ?></code>
                                    <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                        <?php echo htmlspecialchars($sub['course_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        Semester <?php echo htmlspecialchars($sub['semester']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="managepapers.php?subject_id=<?php echo $sub['id']; ?>" class="badge bg-success-subtle text-success text-decoration-none px-2 py-1">
                                        <?php echo (int)$sub['total_papers']; ?> Papers
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="editsubject.php?id=<?php echo $sub['id']; ?>"
                                           class="btn btn-outline-warning"
                                           title="Edit Subject">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="deletesubject.php?id=<?php echo $sub['id']; ?>"
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this subject? All papers under this subject will also be deleted.');"
                                           title="Delete Subject">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No subjects found.
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
