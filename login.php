<?php
/**
 * Orient Academy, Ujjain - Student Login
 */
session_start();
require_once 'includes/db.php';

$error = "";

// If already logged in, redirect
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if(empty($email) || empty($password)){
        $error = "Please enter both your email address and password.";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address.";
    }
    else{
        $stmt = $conn->prepare("SELECT id, fullname, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result && $result->num_rows === 1){
            $user = $result->fetch_assoc();

            if(password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['user_email'] = $user['email'];

                if(isset($_POST['remember'])){
                    setcookie("user_email", $user['email'], time() + (86400 * 30), "/");
                }

                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
                exit();
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with this email address.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<title>Student Login - Orient Academy, Ujjain</title>

<body class="auth-page">

<div class="auth-card">

    <div class="auth-header">
        <div class="brand-logo-icon mx-auto mb-3" style="width:55px; height:55px; font-size:1.6rem;">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h2 class="mb-1">Orient Academy</h2>
        <div class="text-warning small text-uppercase fw-bold letter-spacing-1">Ujjain, M.P.</div>
        <p class="text-white-50 mt-2 mb-0 small">Student Login Portal</p>
    </div>

    <div class="auth-body">

        <?php
        if(isset($_SESSION['success_message'])){
            echo '<div class="alert alert-success d-flex align-items-center gap-2">'
                 . '<i class="bi bi-check-circle-fill"></i> '
                 . htmlspecialchars($_SESSION['success_message']) .
                 '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="mb-3">
                <label class="form-label small fw-bold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="student@example.com"
                           value="<?php echo htmlspecialchars($_COOKIE['user_email'] ?? ($_POST['email'] ?? '')); ?>"
                           required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password"
                           name="password"
                           id="passwordInput"
                           class="form-control"
                           placeholder="Enter your password"
                           required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass()">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-check mb-4">
                <input type="checkbox"
                       class="form-check-input"
                       name="remember"
                       id="rememberMe"
                       <?php echo isset($_COOKIE['user_email']) ? 'checked' : ''; ?>>
                <label class="form-check-label small" for="rememberMe">
                    Remember my email
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login to Account
            </button>

        </form>

        <hr class="my-4">

        <div class="text-center small text-muted">
            Don't have an account? 
            <a href="register.php" class="text-primary fw-bold text-decoration-none">
                Register Here
            </a>
        </div>

        <div class="text-center mt-3 small">
            <a href="index.php" class="text-secondary text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Back to Homepage
            </a>
        </div>

    </div>

</div>

<script>
function togglePass(){
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
    if(input.type === 'password'){
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

</body>
</html>