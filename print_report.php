<?php
require_once __DIR__ . '/auth.php';
requireRole(['hrmo']);

$summary = [
    'employees' => (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn(),
    'pending_hrmo' => (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending HRMO'")->fetchColumn(),
    'pending_head' => (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending Head'")->fetchColumn(),
    'approved' => (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Approved'")->fetchColumn(),
    'rejected' => (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status LIKE 'Rejected%'")->fetchColumn(),
];

$rows = $db->query("
    SELECT u.fullname, u.department,
           u.vacation_leave, u.sick_leave,
           SUM(CASE WHEN lr.status = 'Approved' THEN lr.days ELSE 0 END) AS approved_days
    FROM users u
    LEFT JOIN leave_requests lr ON lr.employee_id = u.id
    WHERE u.role = 'employee'
    GROUP BY u.id
    ORDER BY u.fullname ASC
")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Printable HRMO Report';
include 'layout_top.php';
?>
<div class="card">
    <div class="print-only">
        <h2>ISU Roxas Campus</h2>
        <p><strong>HRMO Leave Monitoring Report</strong></p>
    </div>

    <div class="hero no-print">
        <div>
            <h2>Printable HRMO Report</h2>
            <p>Summary and employee balances formatted for finals presentation or paper output.</p>
        </div>
        <div class="actions">
            <button onclick="window.print()">Print</button>
            <a class="button secondary" href="reports.php">Back</a>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><span>Employees</span><strong><?= $summary['employees'] ?></strong></div>
        <div class="stat"><span>Pending HRMO</span><strong><?= $summary['pending_hrmo'] ?></strong></div>
        <div class="stat"><span>Pending Head</span><strong><?= $summary['pending_head'] ?></strong></div>
        <div class="stat"><span>Approved</span><strong><?= $summary['approved'] ?></strong></div>
        <div class="stat"><span>Rejected</span><strong><?= $summary['rejected'] ?></strong></div>
    </div>

    <table>
        <tr>
            <th>Employee</th>
            <th>Department</th>
            <th>Vacation Balance</th>
            <th>Sick Balance</th>
            <th>Approved Days</th>
        </tr>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['fullname']) ?></td>
                <td><?= e($r['department']) ?></td>
                <td><?= e($r['vacation_leave']) ?></td>
                <td><?= e($r['sick_leave']) ?></td>
                <td><?= e($r['approved_days']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'layout_bottom.php'; ?>
