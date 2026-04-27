<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();
$user = currentUser();

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT lr.*, u.fullname, u.role AS employee_role, u.department, u.email, u.employee_no, u.position FROM leave_requests lr JOIN users u ON u.id = lr.employee_id WHERE lr.id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    flash('error', 'Request not found.');
    redirect('leave_requests.php');
}

$pageTitle = 'View Request';
include 'layout_top.php';
?>
<div class="grid two">
    <div class="card">
        <div class="page-actions">
            <div>
                <h1>Leave Request #<?= e($row['id']) ?></h1>
                <p class="muted">Formal digital leave record with printable layout.</p>
            </div>
            <div class="actions no-print">
                <a class="btn secondary" href="print_leave.php?id=<?= (int)$row['id'] ?>">Print Form</a>
                <a class="btn secondary" href="leave_requests.php">Back</a>
            </div>
        </div>
        <div class="kv">
            <div><strong>Employee No.</strong></div><div><?= e($row['employee_no']) ?></div>
            <div><strong>Name</strong></div><div><?= e($row['fullname']) ?></div>
            <div><strong>Email</strong></div><div><?= e($row['email']) ?></div>
            <div><strong>Position</strong></div><div><?= e($row['position']) ?></div>
            <div><strong>Department</strong></div><div><?= e($row['department']) ?></div>
            <div><strong>User Type</strong></div><div><?= e(roleLabel($row['employee_role'])) ?></div>
            <div><strong>Leave Type</strong></div><div><?= e($row['leave_type']) ?></div>
            <div><strong>Date Range</strong></div><div><?= e($row['start_date']) ?> to <?= e($row['end_date']) ?></div>
            <div><strong>Total Days</strong></div><div><?= e($row['days']) ?></div>
            <div><strong>Commutation</strong></div><div><?= e($row['commutation']) ?></div>
            <div><strong>Specific Details</strong></div><div><?= nl2br(e($row['specific_details'])) ?></div>
            <div><strong>Reason</strong></div><div><?= nl2br(e($row['reason'])) ?></div>
            <div><strong>Status</strong></div><div><span class="badge <?= statusBadgeClass($row['status']) ?>"><?= e($row['status']) ?></span></div>
            <div><strong>Admin Remark</strong></div><div><?= e($row['admin_remark']) ?></div>
            <div><strong>Head Remark</strong></div><div><?= e($row['head_remark']) ?></div>
        </div>
    </div>

    <div class="card status-panel">
        <h2>Digital Approval</h2>
        <p class="muted">Approval is recorded electronically. No manual signature section is used.</p>

        <?php if ($user['role'] === 'admin' && $row['status'] === 'Pending Admin Review'): ?>
            <form method="post" action="process_leave.php?id=<?= (int)$row['id'] ?>&action=endorse" class="approval-form form-grid">
                <label>Admin Remark</label>
                <textarea name="remark" placeholder="Optional admin remark before sending to Head."></textarea>
                <button type="submit">Endorse to Head</button>
            </form>
            <form method="post" action="process_leave.php?id=<?= (int)$row['id'] ?>&action=reject_admin" class="approval-form form-grid">
                <label>Admin Rejection Remark</label>
                <textarea name="remark" placeholder="Explain why the request is being rejected."></textarea>
                <button type="submit" class="btn-danger">Reject Request</button>
            </form>
        <?php elseif ($user['role'] === 'head' && $row['status'] === 'Pending Head Approval'): ?>
            <form method="post" action="process_leave.php?id=<?= (int)$row['id'] ?>&action=approve" class="approval-form form-grid">
                <label>Head Approval Remark</label>
                <textarea name="remark" placeholder="Optional final approval note."></textarea>
                <button type="submit">Approve Request</button>
            </form>
            <form method="post" action="process_leave.php?id=<?= (int)$row['id'] ?>&action=reject_head" class="approval-form form-grid">
                <label>Head Rejection Remark</label>
                <textarea name="remark" placeholder="Explain why the request is being rejected."></textarea>
                <button type="submit" class="btn-danger">Reject Request</button>
            </form>
        <?php else: ?>
            <div class="note-box">
                <strong>Current status</strong>
                <p class="muted">No further action is available for this request in your role.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'layout_bottom.php'; ?>