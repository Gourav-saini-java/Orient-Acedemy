<?php
/**
 * Orient Academy, Ujjain - Global Search Page
 */
require_once 'includes/db.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$results = null;

if(!empty($keyword)){
    $search = "%" . $keyword . "%";

    $stmt = $conn->prepare("
        SELECT
            papers.*,
            courses.course_name,
            subjects.subject_name,
            subjects.subject_code
        FROM papers
        INNER JOIN courses ON papers.course_id = courses.id
        INNER JOIN subjects ON papers.subject_id = subjects.id
        WHERE
            courses.course_name LIKE ?
            OR subjects.subject_name LIKE ?
            OR subjects.subject_code LIKE ?
            OR papers.paper_title LIKE ?
            OR papers.year LIKE ?
            OR papers.semester LIKE ?
        ORDER BY papers.upload_date DESC, papers.year DESC
    ");

    $stmt->bind_param(
        "ssssss",
        $search,
        $search,
        $search,
        $search,
        $search,
        $search
    );

    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<?php include 'includes/header.php'; ?>
<title>Search Results: <?php echo htmlspecialchars($keyword); ?> - Orient Academy, Ujjain</title>

<body>

<?php include 'includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Search Results</h1>
        <p class="lead">
            Showing results for: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong>
        </p>

        <div class="search-box-wrap" style="max-width: 600px;">
            <form action="search.php" method="GET">
                <div class="input-group search-input-group">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Search another subject, course or year..."
                           value="<?php echo htmlspecialchars($keyword); ?>"
                           required>
                    <button class="btn btn-warning text-dark fw-bold px-4" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Results List -->
<section class="container py-5">

    <?php if($results && $results->num_rows > 0): ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                Found <?php echo $results->num_rows; ?> Matching <?php echo ($results->num_rows == 1) ? 'Paper' : 'Papers'; ?>
            </h4>
            <a href="papers.php" class="btn btn-outline-primary btn-sm rounded-pill">
                View All Papers
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
                        <?php while($row = $results->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-semibold">
                                        <?php echo htmlspecialchars($row['course_name']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['subject_name']); ?></strong>
                                    <?php if(!empty($row['subject_code'])): ?>
                                        <div class="small text-muted"><?php echo htmlspecialchars($row['subject_code']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-medium"><?php echo htmlspecialchars($row['paper_title']); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        Sem <?php echo htmlspecialchars($row['semester']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?php echo htmlspecialchars($row['year']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="viewpaper.php?id=<?php echo (int)$row['id']; ?>" target="_blank" class="btn btn-outline-primary" title="View Paper">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="downloadpaper.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-success" title="Download Paper">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>

        <div class="text-center py-5 custom-table-card p-5">
            <i class="bi bi-search display-1 text-secondary"></i>
            <h3 class="mt-3">No Results Found</h3>
            <p class="text-muted">
                We couldn't find any question papers matching "<strong><?php echo htmlspecialchars($keyword); ?></strong>".
            </p>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
                    Back to Home
                </a>
                <a href="papers.php" class="btn btn-primary rounded-pill px-4">
                    Browse All Papers
                </a>
            </div>
        </div>

    <?php endif; ?>

</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>