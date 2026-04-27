<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireRoles(['admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$userRow = $stmt->fetch();

if (!$userRow) {
    flash('error', 'User not found.');
    redirect('manage_users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE users SET employee_no=?, fullname=?, email=?, department=?, position=?, role=?, vacation_leave=?, sick_leave=? WHERE id=?");
    $stmt->execute([
        trim($_POST['employee_no'] ?? ''),
        trim($_POST['fullname'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['department'] ?? ''),
        trim($_POST['position'] ?? ''),
        $_POST['role'] ?? 'teaching',
        (float)($_POST['vacation_leave'] ?? 15),
        (float)($_POST['sick_leave'] ?? 15),
        $id
    ]);
    addAudit($db, currentUser()['id'], 'update_account', 'Updated account #' . $id);
    flash('success', 'Account updated successfully.');
    redirect('manage_users.php');
}

$pageTitle = 'Edit Account';
include 'layout_top.php';
?>
<div class="card">
    <h2>Edit Account</h2>
    <form method="post" class="form-grid">
        <label>Employee No.</label>
        <input type="text" name="employee_no" value="<?= e($userRow['employee_no']) ?>">

        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= e($userRow['fullname']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($userRow['email']) ?>" required>

        <label>Department</label>
        <input type="text" name="department" value="<?= e($userRow['department']) ?>" required>

        <label>Position</label>
        <input type="text" name="position" value="<?= e($userRow['position']) ?>">

        <label>Role</label>
        <select name="role" required>
            <option value="teaching" <?= $userRow['role']==='teaching' ? 'selected' : '' ?>>Teaching Personnel</option>
            <option value="non_teaching" <?= $userRow['role']==='non_teaching' ? 'selected' : '' ?>>Non-Teaching Personnel</option>
            <option value="head" <?= $userRow['role']==='head' ? 'selected' : '' ?>>Head</option>
            <option value="admin" <?= $userRow['role']==='admin' ? 'selected' : '' ?>>Admin / HRMO</option>
        </select>

        <label>Vacation Leave</label>
        <input type="number" step="0.5" name="vacation_leave" value="<?= e($userRow['vacation_leave']) ?>">

        <label>Sick Leave</label>
        <input type="number" step="0.5" name="sick_leave" value="<?= e($userRow['sick_leave']) ?>">

        <button type="submit">Update Account</button>
    </form>
</div>
<?php include 'layout_bottom.php'; ?>