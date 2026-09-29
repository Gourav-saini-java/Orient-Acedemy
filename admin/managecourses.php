<?php
/**
 * Orient Academy, Ujjain - Admin: Manage Courses
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "
SELECT 
    c.*,
    COUNT(DISTINCT s.id) AS total_subjects,
    COUNT(DISTINCT p.id) AS total_papers
FROM courses c
LEFT JOIN subjects s ON c.id = s.course_id
LEFT JOIN papers p ON c.id = p.course_id
WHERE 1=1
";

if(!empty($search)){
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (c.course_name LIKE '%$safeSearch%' OR c.description LIKE '%$safeSearch%')";
}

$sql .= " GROUP BY c.id ORDER BY c.course_name ASC";

$result = mysqli_query($conn, $sql);

$totalCoursesCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'] ?? 0;
?>

<?php include '../includes/header.php'; ?>
<title>Manage Courses - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Manage Academic Courses</h1>
        <p class="lead">Add, edit, or configure degree courses and departments.</p>
    </div>
</section>

<section class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                Courses Catalog
                <span class="badge bg-primary fs-6 ms-2 align-middle"><?php echo (int)$totalCoursesCount; ?> Total</span>
            </h3>
        </div>

        <div class="d-flex gap-2">
            <a href="addcourse.php" class="btn btn-primary rounded-pill px-4 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Add New Course
            </a>
            <a href="admindashboard.php" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form method="GET" action="managecourses.php" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by course name or description..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-semibold rounded-3">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Courses Table -->
    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Course Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th class="text-center">Subjects</th>
                        <th class="text-center">Papers</th>
                        <th class="text-center" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($course = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo $course['id']; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($course['course_name']); ?></strong>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($course['course_slug']); ?></code>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <?php echo htmlspecialchars(mb_strimwidth($course['description'] ?? '', 0, 60, '...')); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="managesubjects.php?course_id=<?php echo $course['id']; ?>" class="badge bg-info-subtle text-info text-decoration-none px-2 py-1">
                                        <?php echo (int)$course['total_subjects']; ?> Subjects
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="managepapers.php?course_id=<?php echo $course['id']; ?>" class="badge bg-success-subtle text-success text-decoration-none px-2 py-1">
                                        <?php echo (int)$course['total_papers']; ?> Papers
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="editcourse.php?id=<?php echo $course['id']; ?>"
                                           class="btn btn-outline-warning"
                                           title="Edit Course">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="deletecourse.php?id=<?php echo $course['id']; ?>"
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this course? All associated subjects and papers will be removed.');"
                                           title="Delete Course">
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
                                No courses found matching your criteria.
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
