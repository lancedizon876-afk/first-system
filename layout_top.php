<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
$user = $_SESSION['user'] ?? null;
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
function navActive(string $page, string $currentPage): string { return $page === $currentPage ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) : 'ISU Leave System' ?></title>
    <link rel="stylesheet" href="style.css">
    <script defer src="app.js"></script>
</head>
<body>
<div class="shell">
    <?php if ($user): ?>
    <aside class="sidebar">
        <div class="brand">
            <div class="logo">ISU</div>
            <div>
                <h2>Leave System</h2>
                <p>Roxas Campus</p>
            </div>
        </div>

        <div class="profile">
            <div class="avatar"><?= strtoupper(substr($user['fullname'], 0, 1)) ?></div>
            <div>
                <strong><?= e($user['fullname']) ?></strong>
                <small><?= e(roleLabel($user['role'])) ?></small>
            </div>
        </div>

        <nav class="menu">
            <a class="<?= navActive(dashboardForRole($user['role']), $currentPage) ?>" href="<?= e(dashboardForRole($user['role'])) ?>">Dashboard</a>
            <?php if (in_array($user['role'], ['teaching', 'non_teaching'])): ?>
                <a class="<?= navActive('apply_leave.php', $currentPage) ?>" href="apply_leave.php">Apply Leave</a>
                <a class="<?= navActive('my_requests.php', $currentPage) ?>" href="my_requests.php">My Requests</a>
            <?php endif; ?>
            <?php if ($user['role'] === 'admin'): ?>
                <a class="<?= navActive('manage_users.php', $currentPage) ?>" href="manage_users.php">Manage Accounts</a>
                <a class="<?= navActive('leave_requests.php', $currentPage) ?>" href="leave_requests.php">All Requests</a>
                <a class="<?= navActive('reports.php', $currentPage) ?>" href="reports.php">Reports</a>
            <?php endif; ?>
            <?php if ($user['role'] === 'head'): ?>
                <a class="<?= navActive('leave_requests.php', $currentPage) ?>" href="leave_requests.php">Final Approval</a>
            <?php endif; ?>
            <a class="<?= navActive('notifications.php', $currentPage) ?>" href="notifications.php">Notifications</a>
            <a class="<?= navActive('change_password.php', $currentPage) ?>" href="change_password.php">Change Password</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>
    <?php endif; ?>

    <main class="content <?= $user ? '' : 'auth-page' ?>">
        <?php if ($msg = flash('success')): ?>
            <div class="alert success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert error"><?= e($msg) ?></div>
        <?php endif; ?>
