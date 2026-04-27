<?php
require_once __DIR__ . '/config.php';

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if (!currentUser()) {
        flash('error', 'Please log in first.');
        redirect('login.php');
    }
}

function requireRoles(array $roles) {
    requireLogin();
    if (!in_array(currentUser()['role'], $roles, true)) {
        http_response_code(403);
        die('Access denied.');
    }
}

function requireRole(array $roles) {
    requireRoles($roles);
}

function refreshSessionUser(PDO $db) {
    if (!currentUser()) return;
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([currentUser()['id']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user'] = $user;
    }
}

function dashboardForRole(string $role): string {
    switch ($role) {
        case 'admin': return 'admin_dashboard.php';
        case 'head': return 'head_dashboard.php';
        case 'teaching': return 'teaching_dashboard.php';
        case 'non_teaching': return 'nonteaching_dashboard.php';
        default: return 'login.php';
    }
}
?>