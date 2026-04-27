<?php
require_once __DIR__ . '/auth.php';
requireRoles(['admin']);

$counts = [
    'admins' => (int)$db->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn(),
    'heads' => (int)$db->query("SELECT COUNT(*) FROM users WHERE role='head'")->fetchColumn(),
    'teaching' => (int)$db->query("SELECT COUNT(*) FROM users WHERE role='teaching'")->fetchColumn(),
    'non_teaching' => (int)$db->query("SELECT COUNT(*) FROM users WHERE role='non_teaching'")->fetchColumn(),
];

$rows = $db->query("
    SELECT fullname, role, department, vacation_leave, sick_leave
    FROM users
    ORDER BY fullname
")->fetchAll();

$pageTitle = 'Reports';
include 'layout_top.php';
?>
<div class="card">
    <div class="hero-head">
        <div>
            <h1>Reports</h1>
            <p>Role summary and account balances.</p>
        </div>
    </div>
</div>
<div class="stats">
    <div class="stat"><span>Admin / HRMO</span><strong><?= $counts['admins'] ?></strong></div>
    <div class="stat"><span>Head</span><strong><?= $counts['heads'] ?></strong></div>
    <div class="stat"><span>Teaching</span><strong><?= $counts['teaching'] ?></strong></div>
    <div class="stat"><span>Non-Teaching</span><strong><?= $counts['non_teaching'] ?></strong></div>
</div>
<div class="card">
    <table>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Department</th>
            <th>Vacation</th>
            <th>Sick</th>
        </tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?= e($r['fullname']) ?></td>
            <td><?= e(roleLabel($r['role'])) ?></td>
            <td><?= e($r['department']) ?></td>
            <td><?= e($r['vacation_leave']) ?></td>
            <td><?= e($r['sick_leave']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'layout_bottom.php'; ?>