<?php
/**
 * Orient Academy, Ujjain - Admin: Manage Users
 */
require_once 'admin_auth.php';
require_once '../includes/db.php';

// Handle user deletion
if(isset($_GET['delete']) && is_numeric($_GET['delete'])){
    $userId = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    header("Location: manageusers.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM users WHERE 1=1";
if(!empty($search)){
    $safe = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (fullname LIKE '%$safe%' OR email LIKE '%$safe%' OR phone LIKE '%$safe%')";
}
$sql .= " ORDER BY id DESC";

$users = $conn->query($sql);

$totalUsersCount = mysqli_fetch_assoc($conn->query("SELECT COUNT(*) AS total FROM users"))['total'] ?? 0;
?>

<?php include '../includes/header.php'; ?>
<title>Manage Students - Orient Academy, Ujjain</title>

<body>

<?php include '../includes/navbar.php'; ?>

<!-- Header -->
<section class="hero text-center py-5">
    <div class="container">
        <h1 class="fw-bold mb-2">Manage Registered Students</h1>
        <p class="lead">View, search, or manage student accounts registered at Orient Academy.</p>
    </div>
</section>

<section class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                Student Directory
                <span class="badge bg-warning text-dark fs-6 ms-2 align-middle"><?php echo (int)$totalUsersCount; ?> Total</span>
            </h3>
        </div>

        <div class="d-flex gap-2">
            <a href="admindashboard.php" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <form method="GET" action="manageusers.php" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by student name, email, or mobile..."
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

    <!-- Table -->
    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Student Name</th>
                        <th>Mobile Number</th>
                        <th>Email Address</th>
                        <th>Registered Date</th>
                        <th class="text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($users && $users->num_rows > 0): ?>
                        <?php while($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo $user['id']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:34px; height:34px; font-size:0.85rem;">
                                            <?php echo strtoupper(substr($user['fullname'], 0, 1)); ?>
                                        </div>
                                        <strong><?php echo htmlspecialchars($user['fullname']); ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        <?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : '-'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="manageusers.php?delete=<?php echo $user['id']; ?>"
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this student account?');"
                                       title="Delete User">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                No registered students found.
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