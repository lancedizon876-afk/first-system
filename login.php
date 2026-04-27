<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (isset($_SESSION['user'])) {
    redirect(dashboardForRole($_SESSION['user']['role']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        flash('success', 'Welcome back, ' . $user['fullname'] . '.');
        redirect(dashboardForRole($user['role']));
    }

    flash('error', 'Invalid email or password.');
    redirect('login.php');
}

$pageTitle = 'Login';
include 'layout_top.php';
?>
<div class="login-wrap">
    <section class="login-card">
        <div class="login-brand-top">
            <img src="assets/logo.png" alt="ISU Logo" class="login-logo" onerror="this.style.display='none'">
            <div class="login-brand-text">
                <span>ISABELA STATE UNIVERSITY</span>
                <small>Roxas Campus</small>
            </div>
        </div>

        <h2>ISU Leave System</h2>
        <p class="login-subtitle">Sign in to access the digital leave management portal.</p>

        <form method="post" class="form-grid" id="loginForm">
            <label>Email address</label>
            <input type="email" name="email" placeholder="name@isu.local" required>

            <label>Password</label>
            <div class="password-wrap">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <button type="button" class="toggle-pass" id="togglePassword">Show</button>
            </div>

            <label class="remember-row">
                <input type="checkbox" name="remember_email" id="rememberEmail">
                <span>Remember email on this browser</span>
            </label>

            <button type="submit" id="loginBtn">
                <span class="btn-text">Login</span>
                <span class="btn-loading">Signing in...</span>
            </button>
        </form>
    </section>
</div>
<?php include 'layout_bottom.php'; ?>
