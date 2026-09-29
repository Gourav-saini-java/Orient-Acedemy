<?php
/**
 * Orient Academy, Ujjain - Question Papers Directory & Filters
 */
require_once 'includes/db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course_id = isset($_GET['course_id']) && is_numeric($_GET['course_id']) ? (int)$_GET['course_id'] : (isset($_GET['course']) && is_numeric($_GET['course']) ? (int)$_GET['course'] : 0);
$subject_id = isset($_GET['subject_id']) && is_numeric($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
$semester = isset($_GET['semester']) ? trim($_GET['semester']) : '';
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? (int)$_GET['year'] : 0;

// Base query with prepared statement parameters
$query = "
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

$params = [];
$types = "";

if(!empty($search)){
    $query .= " AND (p.paper_title LIKE ? OR s.subject_name LIKE ? OR s.subject_code LIKE ?)";
    $like_search = "%" . $search . "%";
    $params[] = $like_search;
    $params[] = $like_search;
    $params[] = $like_search;
    $types .= "sss";
}

if($course_id > 0){
    $query .= " AND p.course_id = ?";
    $params[] = $course_id;
    $types .= "i";
}

if($subject_id > 0){
    $query .= " AND p.subject_id = ?";
    $params[] = $subject_id;
    $types .= "i";
}

if(!empty($semester)){
    $query .= " AND p.semester = ?";
    $params[] = $semester;
    $types .= "s";
}

if($year > 0){
    $query .= " AND p.year = ?";
    $params[] = $year;
    $types .= "i";
}

$query .= " ORDER BY p.upload_date DESC, p.year DESC, p.id DESC";

$stmt = $conn->prepare($query);

if(!empty($types)){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$papers_result = $stmt->get_result();

// Fetch filter options
$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");

$subjects_filter_sql = "SELECT id, subject_name, course_id FROM subjects ORDER BY subject_name ASC";
if($course_id > 0){
    $subjects_filter_sql = "SELECT id, subject_name, course_id FROM subjects WHERE course_id = $course_id ORDER BY subject_name ASC";
}
$subjects_res = mysqli_query($conn, $subjects_filter_sql);

// Fetch distinct years
$years_res = mysqli_query($conn, "SELECT DISTINCT year FROM papers ORDER BY year DESC");
?>

<?php include 'includes/header.php'; ?>
<title>Previous Year Question Papers - Orient Academy, Ujjain</title>

<body>

<?php include 'includes/navbar.php'; ?>

<!-- Filter & Search Hero Section -->
<section class="hero text-center py-5">
    <div class="container">

        <h1 class="fw-bold mb-2">Question Papers Archive</h1>
        <p class="lead mb-4">
            Search and download Previous Year Question Papers (PYQs) for all semesters.
        </p>

        <!-- Advanced Filter Form -->
        <div class="card shadow-lg border-0 rounded-4 text-start text-dark p-4 mx-auto" style="max-width: 1000px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
            <form method="GET" action="papers.php" class="row g-3">

                <!-- Text Search -->
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Search Keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Paper title, subject..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <!-- Course Dropdown -->
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-muted">Course</label>
                    <select name="course_id" id="filter_course" class="form-select">
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

                <!-- Semester Dropdown -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="">All Sem</option>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo ($semester == (string)$i) ? 'selected' : ''; ?>>
                                Sem <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Year Dropdown -->
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All Years</option>
                        <?php if($years_res): ?>
                            <?php while($y = mysqli_fetch_assoc($years_res)): ?>
                                <option value="<?php echo $y['year']; ?>" <?php echo ($year == (int)$y['year']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($y['year']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Submit / Reset Buttons -->
                <div class="col-md-1 col-sm-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" title="Apply Filter">
                        <i class="bi bi-filter"></i>
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>

<!-- Papers Cards Grid -->
<section class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold mb-0">
            Available Papers
            <span class="badge bg-primary fs-6 ms-2 align-middle">
                <?php echo $papers_result ? $papers_result->num_rows : 0; ?>
            </span>
        </h3>

        <?php if(!empty($search) || $course_id > 0 || !empty($semester) || $year > 0): ?>
            <a href="papers.php" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-x-circle me-1"></i> Clear Filters
            </a>
        <?php endif; ?>
    </div>

    <?php if($papers_result && $papers_result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while($paper = $papers_result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card paper-card h-100">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                                <?php echo htmlspecialchars($paper['course_name']); ?>
                            </span>

                            <?php if(!empty($paper['is_latest'])): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                    <i class="bi bi-stars me-1"></i> Latest
                                </span>
                            <?php endif; ?>
                        </div>

                        <h4 class="paper-card-title flex-grow-1">
                            <?php echo htmlspecialchars($paper['paper_title']); ?>
                        </h4>

                        <div class="text-muted small mb-3 d-flex flex-column gap-1">
                            <div>
                                <i class="bi bi-book me-1 text-primary"></i>
                                <strong>Subject:</strong> <?php echo htmlspecialchars($paper['subject_name']); ?>
                                <?php if(!empty($paper['subject_code'])): ?>
                                    (<code><?php echo htmlspecialchars($paper['subject_code']); ?></code>)
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-3">
                                <span><i class="bi bi-layers me-1 text-warning"></i> Sem <?php echo htmlspecialchars($paper['semester']); ?></span>
                                <span><i class="bi bi-calendar3 me-1 text-info"></i> Year <?php echo htmlspecialchars($paper['year']); ?></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 px-3 bg-light rounded-3 mb-3 small text-muted">
                            <span><i class="bi bi-eye text-primary me-1"></i> <?php echo (int)$paper['views']; ?> Views</span>
                            <span><i class="bi bi-download text-success me-1"></i> <?php echo (int)$paper['downloads']; ?> Downloads</span>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="viewpaper.php?id=<?php echo (int)$paper['id']; ?>"
                               target="_blank"
                               class="btn btn-outline-primary flex-grow-1 fw-semibold rounded-pill">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="downloadpaper.php?id=<?php echo (int)$paper['id']; ?>"
                               class="btn btn-success flex-grow-1 fw-semibold rounded-pill">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 custom-table-card p-5">
            <i class="bi bi-search display-1 text-secondary"></i>
            <h3 class="mt-3">No Question Papers Found</h3>
            <p class="text-muted">No papers match your search criteria. Try modifying your filter or clear filters to view all papers.</p>
            <a href="papers.php" class="btn btn-primary rounded-pill px-4 mt-2">
                View All Question Papers
            </a>
        </div>
    <?php endif; ?>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>