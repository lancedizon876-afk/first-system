<?php
require_once __DIR__ . '/auth.php';
requireRoles(['teaching']);
refreshSessionUser($db);
$user = currentUser();

$stmt = $db->prepare("SELECT COUNT(*) FROM leave_requests WHERE employee_id = ?");
$stmt->execute([$user['id']]);
$total = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM leave_requests WHERE employee_id = ? AND status = 'Approved'");
$stmt->execute([$user['id']]);
$approved = (int)$stmt->fetchColumn();

$pageTitle = 'Teaching Personnel Dashboard';
include 'layout_top.php';
?>
<div class="card">
    <div class="hero-head">
        <div>
            <h1>Teaching Personnel Dashboard</h1>
            <p>Apply for leave, monitor requests, and check your current leave balance.</p>
        </div>
        <div class="actions">
            <a class="btn" href="apply_leave.php">Apply Leave</a>
            <a class="btn secondary" href="my_requests.php">My Requests</a>
        </div>
    </div>
</div>
<div class="stats">
    <div class="stat"><span>Vacation Leave</span><strong><?= e($user['vacation_leave']) ?></strong></div>
    <div class="stat"><span>Sick Leave</span><strong><?= e($user['sick_leave']) ?></strong></div>
    <div class="stat"><span>Total Requests</span><strong><?= $total ?></strong></div>
    <div class="stat"><span>Approved</span><strong><?= $approved ?></strong></div>
</div>
<?php include 'layout_bottom.php'; ?>