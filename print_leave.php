<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
$user = currentUser();
$id = (int)($_GET['id'] ?? 0);

$sql = "SELECT lr.*, u.fullname, u.email, u.employee_no, u.department, u.position, u.role AS employee_role FROM leave_requests lr JOIN users u ON u.id = lr.employee_id WHERE lr.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    flash('error', 'Leave request not found.');
    redirect('my_requests.php');
}
if (in_array($user['role'], ['teaching','non_teaching'], true) && (int)$row['employee_id'] !== (int)$user['id']) {
    http_response_code(403);
    die('Access denied.');
}

$pageTitle = 'Print Leave Form';
include 'layout_top.php';
?>
<div class="print-sheet">
    <div class="print-header">
        <h2>ISABELA STATE UNIVERSITY - ROXAS CAMPUS</h2>
        <h3>Digital Application for Leave</h3>
        <p>System-generated printable copy for defense and records</p>
    </div>

    <div class="print-grid">
        <div class="print-box">
            <h3>Employee Details</h3>
            <div class="kv">
                <div><strong>Employee No.</strong></div><div><?= e($row['employee_no']) ?></div>
                <div><strong>Name</strong></div><div><?= e($row['fullname']) ?></div>
                <div><strong>Email</strong></div><div><?= e($row['email']) ?></div>
                <div><strong>Position</strong></div><div><?= e($row['position']) ?></div>
                <div><strong>Department</strong></div><div><?= e($row['department']) ?></div>
                <div><strong>User Type</strong></div><div><?= e(roleLabel($row['employee_role'])) ?></div>
                <div><strong>Date Filed</strong></div><div><?= e(date('F d, Y', strtotime($row['created_at']))) ?></div>
            </div>
        </div>
        <div class="print-box">
            <h3>Leave Details</h3>
            <div class="kv">
                <div><strong>Leave Type</strong></div><div><?= e($row['leave_type']) ?></div>
                <div><strong>Inclusive Dates</strong></div><div><?= e($row['start_date']) ?> to <?= e($row['end_date']) ?></div>
                <div><strong>Total Days</strong></div><div><?= e($row['days']) ?></div>
                <div><strong>Commutation</strong></div><div><?= e($row['commutation']) ?></div>
                <div><strong>Specific Details</strong></div><div><?= nl2br(e($row['specific_details'])) ?></div>
                <div><strong>Reason</strong></div><div><?= nl2br(e($row['reason'])) ?></div>
            </div>
        </div>
    </div>

    <div class="print-grid" style="margin-top:18px;">
        <div class="print-box">
            <h3>Admin Review</h3>
            <p><strong>Status:</strong> <?= e($row['status']) ?></p>
            <p><strong>Remark:</strong> <?= e($row['admin_remark']) ?></p>
            <p class="muted small">Digitally recorded in the leave management system.</p>
        </div>
        <div class="print-box">
            <h3>Head Approval</h3>
            <p><strong>Status:</strong> <?= e($row['status']) ?></p>
            <p><strong>Remark:</strong> <?= e($row['head_remark']) ?></p>
            <p class="muted small">No signature field is required for this digital workflow.</p>
        </div>
    </div>

    <div class="actions no-print" style="margin-top:18px;">
        <button onclick="window.print()">Print</button>
        <a class="btn secondary" href="javascript:history.back()">Back</a>
    </div>
</div>
<?php include 'layout_bottom.php'; ?>