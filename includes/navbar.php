<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm main-navbar">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/PYQ/index.php">
            <span class="brand-logo-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </span>
            <span class="d-flex flex-column">
                <span class="brand-title">Orient Academy</span>
                <span class="brand-subtitle text-warning">Ujjain</span>
            </span>
        </a>

        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Primary Navigation Menu -->
            <ul class="navbar-nav me-auto ms-lg-4">

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"
                       href="/PYQ/index.php">
                        <i class="bi bi-house-door me-1"></i> Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'courses.php') ? 'active' : ''; ?>"
                       href="/PYQ/courses.php">
                        <i class="bi bi-grid me-1"></i> Courses
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'subjects.php') ? 'active' : ''; ?>"
                       href="/PYQ/subjects.php">
                        <i class="bi bi-book me-1"></i> Subjects
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'papers.php') ? 'active' : ''; ?>"
                       href="/PYQ/papers.php">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Question Papers
                    </a>
                </li>

            </ul>

            <!-- Right Authentication / Account Menu -->
            <ul class="navbar-nav align-items-lg-center">

<?php if(isset($_SESSION['admin_logged_in'])): ?>

    <!-- Logged in as Admin -->
    <li class="nav-item me-2 mb-2 mb-lg-0">
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
            <i class="bi bi-shield-check me-1"></i> Admin: <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </span>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-1"
           href="#"
           id="adminDropdown"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">
            <i class="bi bi-gear-fill"></i> Management
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="adminDropdown">
            <li>
                <a class="dropdown-item" href="/PYQ/admin/admindashboard.php">
                    <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="/PYQ/admin/managecourses.php">
                    <i class="bi bi-mortarboard me-2 text-danger"></i> Manage Courses
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="/PYQ/admin/managesubjects.php">
                    <i class="bi bi-book me-2 text-info"></i> Manage Subjects
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="/PYQ/admin/managepapers.php">
                    <i class="bi bi-file-earmark-pdf me-2 text-success"></i> Manage Papers
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="/PYQ/admin/manageusers.php">
                    <i class="bi bi-people me-2 text-warning"></i> Manage Users
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="/PYQ/admin/reports.php">
                    <i class="bi bi-bar-chart-line me-2 text-purple"></i> Reports & Analytics
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="/PYQ/admin/adminlogout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Admin Logout
                </a>
            </li>
        </ul>
    </li>

<?php elseif(isset($_SESSION['user_id'])): ?>

    <!-- Logged in as Student / User -->
    <li class="nav-item me-2 mb-2 mb-lg-0">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
            <i class="bi bi-person-check me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white"
           href="#"
           id="userDropdown"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i> Account
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
            <li>
                <span class="dropdown-item-text text-muted small">
                    Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></strong>
                </span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="/PYQ/papers.php">
                    <i class="bi bi-collection-play me-2 text-primary"></i> Browse Papers
                </a>
            </li>
            <li>
                <a class="dropdown-item text-danger" href="/PYQ/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </li>
        </ul>
    </li>

<?php else: ?>

    <!-- Guest / Visitor -->
    <li class="nav-item me-2 mb-2 mb-lg-0">
        <a class="btn btn-outline-light btn-sm px-3" href="/PYQ/login.php">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </a>
    </li>

    <li class="nav-item me-2 mb-2 mb-lg-0">
        <a class="btn btn-warning btn-sm px-3 text-dark fw-semibold" href="/PYQ/register.php">
            <i class="bi bi-person-plus-fill me-1"></i> Register
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link text-white-50 small" href="/PYQ/admin/adminlogin.php" title="Staff / Admin Portal">
            <i class="bi bi-shield-lock"></i> Admin
        </a>
    </li>

<?php endif; ?>

            </ul>
        </div>

    </div>
</nav>