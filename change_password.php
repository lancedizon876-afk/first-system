<?php
require_once __DIR__ . '/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([currentUser()['id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password'])) {
        flash('error', 'Current password is incorrect.');
        redirect('change_password.php');
    }
    if (strlen($new) < 6) {
        flash('error', 'New password must be at least 6 characters.');
        redirect('change_password.php');
    }
    if ($new !== $confirm) {
        flash('error', 'Password confirmation does not match.');
        redirect('change_password.php');
    }

    $stmt = $db->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->execute([password_hash($new, PASSWORD_DEFAULT), currentUser()['id']]);
    flash('success', 'Password updated successfully.');
    redirect(dashboardForRole(currentUser()['role']));
}

$pageTitle = 'Change Password';
include 'layout_top.php';
?>
<div class="card">
    <h1>Change Password</h1>
    <form method="post" class="form-grid">
        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Update Password</button>
    </form>
</div>
<?php include 'layout_bottom.php'; ?>