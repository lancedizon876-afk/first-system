<?php
require_once __DIR__ . '/auth.php';
requireRole(['hrmo']);
require_once __DIR__ . '/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'employee'");
$stmt->execute([$id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    flash('error', 'Employee not found.');
    redirect('manage_employees.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("
        UPDATE users
        SET employee_no = ?, fullname = ?, email = ?, department = ?, vacation_leave = ?, sick_leave = ?
        WHERE id = ? AND role = 'employee'
    ");
    $stmt->execute([
        trim($_POST['employee_no'] ?? ''),
        trim($_POST['fullname'] ?? ''),
        trim($_POST['email'] ?? ''),
        trim($_POST['department'] ?? ''),
        (float)($_POST['vacation_leave'] ?? 0),
        (float)($_POST['sick_leave'] ?? 0),
        $id
    ]);

    addAudit($db, (int)currentUser()['id'], 'update_employee', 'HRMO updated employee #' . $id);
    flash('success', 'Employee updated successfully.');
    redirect('manage_employees.php');
}

$pageTitle = 'Edit Employee';
include 'layout_top.php';
?>
<div class="card narrow">
    <h2>Edit Employee</h2>
    <form method="post" class="form-grid">
        <label>Employee No.</label>
        <input type="text" name="employee_no" value="<?= e($employee['employee_no']) ?>">

        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= e($employee['fullname']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($employee['email']) ?>" required>

        <label>Department</label>
        <input type="text" name="department" value="<?= e($employee['department']) ?>">

        <label>Vacation Leave</label>
        <input type="number" step="0.5" name="vacation_leave" value="<?= e($employee['vacation_leave']) ?>">

        <label>Sick Leave</label>
        <input type="number" step="0.5" name="sick_leave" value="<?= e($employee['sick_leave']) ?>">

        <button type="submit">Update Employee</button>
    </form>
</div>
<?php include 'layout_bottom.php'; ?>