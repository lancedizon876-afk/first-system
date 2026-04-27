<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
$user = currentUser();

if ($user['role'] === 'admin') {
    $stmt = $db->query("SELECT lr.*, u.fullname, u.role AS employee_role, u.department, u.position, u.email FROM leave_requests lr JOIN users u ON u.id = lr.employee_id ORDER BY lr.created_at DESC");
} elseif ($user['role'] === 'head') {
    $stmt = $db->query("SELECT lr.*, u.fullname, u.role AS employee_role, u.department, u.position, u.email FROM leave_requests lr JOIN users u ON u.id = lr.employee_id WHERE lr.status IN ('Pending Head Approval','Approved','Rejected by Head') ORDER BY lr.created_at DESC");
} else {
    http_response_code(403);
    die('Access denied.');
}
$rows = $stmt->fetchAll();

$pageTitle = 'Leave Requests';
include 'layout_top.php';
?>
<div class="card">
    <div class="page-actions">
        <div>
            <h1><?= $user['role'] === 'admin' ? 'All Leave Requests' : 'Final Approval Queue' ?></h1>
            <p class="muted">Digital approval workflow only. No signatures are required.</p>
        </div>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>User Type</th>
            <th>Department</th>
            <th>Leave</th>
            <th>Days</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['id']) ?></td>
                <td><?= e($r['fullname']) ?></td>
                <td><?= e(roleLabel($r['employee_role'])) ?></td>
                <td><?= e($r['department']) ?></td>
                <td><?= e($r['leave_type']) ?></td>
                <td><?= e($r['days']) ?></td>
                <td><span class="badge <?= statusBadgeClass($r['status']) ?>"><?= e($r['status']) ?></span></td>
                <td>
                    <a href="view_request.php?id=<?= (int)$r['id'] ?>">Open</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'layout_bottom.php'; ?>