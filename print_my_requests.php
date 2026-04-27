<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireRoles(['teaching','non_teaching']);
$user = currentUser();

$stmt = $db->prepare("SELECT * FROM leave_requests WHERE employee_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Print My Requests';
include 'layout_top.php';
?>
<div class="print-sheet">
    <div class="print-header">
        <h2>ISABELA STATE UNIVERSITY - ROXAS CAMPUS</h2>
        <h3>Employee Leave Request History</h3>
        <p>Name: <?= e($user['fullname']) ?></p>
    </div>

    <div class="actions no-print" style="margin-bottom:16px;">
        <button onclick="window.print()">Print</button>
        <a class="btn secondary" href="my_requests.php">Back</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Days</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= e($row['id']) ?></td>
                <td><?= e($row['leave_type']) ?></td>
                <td><?= e($row['days']) ?></td>
                <td><?= e($row['start_date']) ?></td>
                <td><?= e($row['end_date']) ?></td>
                <td><?= e($row['status']) ?></td>
                <td><?= e($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'layout_bottom.php'; ?>