<?php
require_once __DIR__ . '/auth.php';
requireRole(['hrmo']);
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeNo = trim($_POST['employee_no'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $vacation = (float)($_POST['vacation_leave'] ?? 0);
    $sick = (float)($_POST['sick_leave'] ?? 0);
    $password = $_POST['password'] ?? '123456';

    if ($fullname === '' || $email === '') {
        flash('error', 'Full name and email are required.');
        redirect('manage_employees.php');
    }

    $stmt = $db->prepare("
        INSERT INTO users (employee_no, fullname, email, password, role, department, vacation_leave, sick_leave)
        VALUES (?, ?, ?, ?, 'employee', ?, ?, ?)
    ");

    try {
        $stmt->execute([$employeeNo, $fullname, $email, password_hash($password, PASSWORD_DEFAULT), $department, $vacation, $sick]);
        addAudit($db, (int)currentUser()['id'], 'create_employee', 'HRMO created employee ' . $fullname);
        flash('success', 'Employee created successfully.');
    } catch (Throwable $e) {
        flash('error', 'Could not create employee: ' . $e->getMessage());
    }
    redirect('manage_employees.php');
}

$employees = $db->query("SELECT * FROM users WHERE role = 'employee' ORDER BY fullname ASC")->fetchAll(PDO::FETCH_ASSOC);
$pageTitle = 'Manage Employees';
include 'layout_top.php';
?>
<div class="grid two">
    <div class="card">
        <h2>Add Employee</h2>
        <form method="post" class="form-grid">
            <label>Employee No.</label>
            <input type="text" name="employee_no">

            <label>Full Name</label>
            <input type="text" name="fullname" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Department</label>
            <input type="text" name="department">

            <label>Vacation Leave</label>
            <input type="number" step="0.5" name="vacation_leave" value="15">

            <label>Sick Leave</label>
            <input type="number" step="0.5" name="sick_leave" value="15">

            <label>Temporary Password</label>
            <input type="text" name="password" value="123456">

            <button type="submit">Save Employee</button>
        </form>
    </div>

    <div class="card">
        <h2>Employee List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Dept</th>
                <th>Vacation</th>
                <th>Sick</th>
                <th>Action</th>
            </tr>
            <?php foreach ($employees as $e): ?>
                <tr>
                    <td><?= e($e['fullname']) ?></td>
                    <td><?= e($e['email']) ?></td>
                    <td><?= e($e['department']) ?></td>
                    <td><?= e($e['vacation_leave']) ?></td>
                    <td><?= e($e['sick_leave']) ?></td>
                    <td><a href="edit_employee.php?id=<?= (int)$e['id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include 'layout_bottom.php'; ?>