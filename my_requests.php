<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireRoles(['teaching', 'non_teaching']);
$user = currentUser();

$stmt = $db->prepare("SELECT * FROM leave_requests WHERE employee_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();

$pageTitle = 'My Requests';
include 'layout_top.php';
?>
<div class="card">
    <div class="page-actions">
        <div>
            <h1>My Leave Requests</h1>
            <p class="muted">Track submitted requests and open the clean printable layout for defense.</p>
        </div>
        <div class="actions no-print">
            <a class="btn" href="apply_leave.php">New Leave Request</a>
            <a class="btn secondary" href="print_my_requests.php">Print History</a>
        </div>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Leave Type</th>
            <th>Days</th>
            <th>Date Range</th>
            <th>Status</th>
            <th>Created</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= e($row['id']) ?></td>
                <td><?= e($row['leave_type']) ?></td>
                <td><?= e($row['days']) ?></td>
                <td><?= e($row['start_date']) ?> to <?= e($row['end_date']) ?></td>
                <td><span class="badge <?= statusBadgeClass($row['status']) ?>"><?= e($row['status']) ?></span></td>
                <td><?= e($row['created_at']) ?></td>
                <td><a href="print_leave.php?id=<?= (int)$row['id'] ?>">Print</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'layout_bottom.php'; ?>