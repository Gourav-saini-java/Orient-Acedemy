<?php
/**
 * Orient Academy, Ujjain - Student Registration
 */
session_start();
require_once 'includes/db.php';

$error = "";

if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if(empty($fullname) || empty($phone) || empty($email) || empty($password) || empty($confirm_password)){
        $error = "All fields are required.";
    }
    elseif(strlen($fullname) < 3){
        $error = "Full name must contain at least 3 characters.";
    }
    elseif(!preg_match('/^[0-9]{10}$/', $phone)){
        $error = "Please enter a valid 10-digit mobile number.";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Please enter a valid email address.";
    }
    elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters long.";
    }
    elseif($password !== $confirm_password){
        $error = "Passwords do not match.";
    }
    else{
        // Check duplicate email or phone
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $check->bind_param("ss", $email, $phone);
        $check->execute();
        $res = $check->get_result();

        if($res && $res->num_rows > 0){
            $error = "An account with this email or mobile number already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (fullname, phone, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $phone, $email, $hashedPassword);

            if($stmt->execute()){
                $_SESSION['success_message'] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed due to a server error. Please try again.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<title>Student Registration - Orient Academy, Ujjain</title>

<body class="auth-page">

<div class="auth-card" style="max-width: 580px;">

    <div class="auth-header">
        <div class="brand-logo-icon mx-auto mb-3" style="width:55px; height:55px; font-size:1.6rem;">
            <i class="bi bi-person-plus-fill"></i>
        </div>
        <h2 class="mb-1">Orient Academy</h2>
        <div class="text-warning small text-uppercase fw-bold letter-spacing-1">Ujjain, M.P.</div>
        <p class="text-white-50 mt-2 mb-0 small">Create Student Account</p>
    </div>

    <div class="auth-body">

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="mb-3">
                <label class="form-label small fw-bold">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text"
                           name="fullname"
                           class="form-control"
                           placeholder="Enter your full name"
                           value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>"
                           required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Mobile Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">+91</span>
                        <input type="tel"
                               name="phone"
                               class="form-control"
                               placeholder="10-digit number"
                               maxlength="10"
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                               required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="student@example.com"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               required>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Password</label>
                    <input type="password"
                           name="password"
                           id="regPassword"
                           class="form-control"
                           placeholder="Min 6 characters"
                           required>
                    <div id="strengthIndicator" class="small mt-1"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold">Confirm Password</label>
                    <input type="password"
                           name="confirm_password"
                           id="confirmPassword"
                           class="form-control"
                           placeholder="Re-enter password"
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-warning text-dark fw-bold w-100 py-2 rounded-pill">
                <i class="bi bi-person-check-fill me-1"></i> Register Account
            </button>

        </form>

        <hr class="my-4">

        <div class="text-center small text-muted">
            Already registered? 
            <a href="login.php" class="text-primary fw-bold text-decoration-none">
                Login Here
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
// Prevent non-digit mobile input
document.querySelector('input[name="phone"]').addEventListener('input', function(){
    this.value = this.value.replace(/\D/g, '');
});

// Live password strength indicator
document.getElementById('regPassword').addEventListener('input', function(){
    const pwd = this.value;
    const indicator = document.getElementById('strengthIndicator');
    if(!pwd){
        indicator.innerHTML = '';
        return;
    }
    if(pwd.length < 6){
        indicator.innerHTML = '<span class="text-danger"><i class="bi bi-shield-x"></i> Weak (min 6 chars)</span>';
    } else if(pwd.length < 10){
        indicator.innerHTML = '<span class="text-warning"><i class="bi bi-shield-slash"></i> Moderate strength</span>';
    } else {
        indicator.innerHTML = '<span class="text-success"><i class="bi bi-shield-check"></i> Strong password</span>';
    }
});
</script>

</body>
</html>