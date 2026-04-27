<?php
require_once __DIR__ . '/auth.php';
requireRoles(['head']);

$pending = (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status='Pending Head Approval'")->fetchColumn();
$approved = (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status='Approved'")->fetchColumn();

$pageTitle = 'Head Dashboard';
include 'layout_top.php';
?>
<div class="card">
    <div class="hero-head">
        <div>
            <h1>Head Dashboard</h1>
            <p>Review endorsed leave applications and decide final approval.</p>
        </div>
        <div class="actions">
            <a class="btn" href="leave_requests.php">Final Approval Queue</a>
        </div>
    </div>
</div>
<div class="stats">
    <div class="stat"><span>Pending Final Approval</span><strong><?= $pending ?></strong></div>
    <div class="stat"><span>Approved</span><strong><?= $approved ?></strong></div>
</div>
<?php include 'layout_bottom.php'; ?>