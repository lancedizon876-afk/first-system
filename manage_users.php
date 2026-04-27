<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireRoles(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeNo = trim($_POST['employee_no'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $role = $_POST['role'] ?? '';
    $vacation = (float)($_POST['vacation_leave'] ?? 15);
    $sick = (float)($_POST['sick_leave'] ?? 15);
    $password = $_POST['password'] ?? '123456';

    if (!in_array($role, ['admin', 'head', 'teaching', 'non_teaching'], true)) {
        flash('error', 'Invalid role.');
        redirect('manage_users.php');
    }

    $stmt = $db->prepare("INSERT INTO users (employee_no, fullname, email, password, role, department, position, vacation_leave, sick_leave) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$employeeNo, $fullname, $email, password_hash($password, PASSWORD_DEFAULT), $role, $department, $position, $vacation, $sick]);
        addAudit($db, currentUser()['id'], 'create_account', 'Created account for ' . $fullname . ' with role ' . $role);
        flash('success', 'Account created successfully.');
    } catch (Throwable $e) {
        flash('error', 'Could not create account: ' . $e->getMessage());
    }
    redirect('manage_users.php');
}

$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'Manage Accounts';
include 'layout_top.php';
?>
<div class="grid two">
    <div class="card">
        <h2>Create Account</h2>
        <p class="muted">Only Admin can register users. No public registration form is shown to end users.</p>
        <form method="post" class="form-grid">
            <label>Employee No.</label>
            <input type="text" name="employee_no">

            <label>Full Name</label>
            <input type="text" name="fullname" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Department</label>
            <input type="text" name="department" required>

            <label>Position</label>
            <input type="text" name="position" placeholder="Instructor I, Administrative Aide, etc.">

            <label>Role</label>
            <select name="role" required>
                <option value="teaching">Teaching Personnel</option>
                <option value="non_teaching">Non-Teaching Personnel</option>
                <option value="head">Head</option>
                <option value="admin">Admin / HRMO</option>
            </select>

            <label>Vacation Leave</label>
            <input type="number" name="vacation_leave" step="0.5" value="15">

            <label>Sick Leave</label>
            <input type="number" name="sick_leave" step="0.5" value="15">

            <label>Temporary Password</label>
            <input type="text" name="password" value="123456">

            <button type="submit">Create Account</button>
        </form>
    </div>

    <div class="card">
        <h2>Accounts</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Action</th>
            </tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['fullname']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e(roleLabel($u['role'])) ?></td>
                <td><?= e($u['department']) ?></td>
                <td><a href="edit_user.php?id=<?= (int)$u['id'] ?>">Edit</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include 'layout_bottom.php'; ?>