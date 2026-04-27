<?php
require_once __DIR__ . '/auth.php';
requireRoles(['admin']);

$totalUsers = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingHrmo = (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status='Pending Admin Review'")->fetchColumn();
$pendingHead = (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status='Pending Head Approval'")->fetchColumn();
$approved = (int)$db->query("SELECT COUNT(*) FROM leave_requests WHERE status='Approved'")->fetchColumn();

$pageTitle = 'Admin Dashboard';
include 'layout_top.php';
?>
<div class="card">
    <div class="hero-head">
        <div>
            <h1>Admin Dashboard</h1>
            <p>Manage accounts, review requests, and monitor system activity.</p>
        </div>
        <div class="actions">
            <a class="btn" href="manage_users.php">Manage Accounts</a>
            <a class="btn secondary" href="leave_requests.php">Open Requests</a>
        </div>
    </div>
</div>

<div class="stats">
    <div class="stat"><span>Total Accounts</span><strong><?= $totalUsers ?></strong></div>
    <div class="stat"><span>Pending Admin Review</span><strong><?= $pendingHrmo ?></strong></div>
    <div class="stat"><span>Pending Head Approval</span><strong><?= $pendingHead ?></strong></div>
    <div class="stat"><span>Approved Requests</span><strong><?= $approved ?></strong></div>
</div>
<?php include 'layout_bottom.php'; ?>