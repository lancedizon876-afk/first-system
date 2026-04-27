<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireRoles(['teaching', 'non_teaching']);
refreshSessionUser($db);
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leaveType = $_POST['leave_type'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $reason = trim($_POST['reason'] ?? '');
    $commutation = trim($_POST['commutation'] ?? 'Requested');
    $specificDetails = trim($_POST['specific_details'] ?? '');

    if (!in_array($leaveType, leaveTypeOptions(), true)) {
        flash('error', 'Invalid leave type.');
        redirect('apply_leave.php');
    }
    if (!$startDate || !$endDate) {
        flash('error', 'Please complete the leave dates.');
        redirect('apply_leave.php');
    }

    $days = calculateInclusiveDays($startDate, $endDate);
    if ($days <= 0) {
        flash('error', 'End date must be on or after the start date.');
        redirect('apply_leave.php');
    }

    if (in_array($leaveType, ['Vacation', 'Sick'], true)) {
        $col = leaveBalanceColumn($leaveType);
        if ($days > (float)$user[$col]) {
            flash('error', 'Requested days exceed available balance.');
            redirect('apply_leave.php');
        }
    }

    $stmt = $db->prepare("INSERT INTO leave_requests (employee_id, leave_type, days, start_date, end_date, reason, status, commutation, specific_details) VALUES (?, ?, ?, ?, ?, ?, 'Pending Admin Review', ?, ?)");
    $stmt->execute([$user['id'], $leaveType, $days, $startDate, $endDate, $reason, $commutation, $specificDetails]);

    notifyRole($db, 'admin', 'New leave request from ' . $user['fullname'] . ' needs admin review.');
    addAudit($db, $user['id'], 'submit_leave', 'Submitted leave request #' . $db->lastInsertId());
    flash('success', 'Leave request submitted successfully.');
    redirect('my_requests.php');
}

$pageTitle = 'Apply Leave';
include 'layout_top.php';
?>
<div class="card">
    <div class="page-actions">
        <div>
            <h1>Digital Leave Application</h1>
            <p class="muted">Separate filing page with auto-calculated leave days and a clean printable format.</p>
        </div>
        <a class="btn secondary" href="my_requests.php">View My Requests</a>
    </div>

    <div class="form-note">Signature lines were removed for digital filing. Admin and Head approval are recorded in the system.</div>

    <form method="post" class="form-grid two-col" id="leaveForm">
        <div>
            <label>Employee Name</label>
            <input type="text" value="<?= e($user['fullname']) ?>" readonly>
        </div>
        <div>
            <label>Department / Office</label>
            <input type="text" value="<?= e($user['department']) ?>" readonly>
        </div>
        <div>
            <label>Position</label>
            <input type="text" value="<?= e($user['position'] ?? '') ?>" readonly>
        </div>
        <div>
            <label>Date Filed</label>
            <input type="date" value="<?= date('Y-m-d') ?>" readonly>
        </div>

        <div>
            <label>Leave Type</label>
            <select name="leave_type" id="leave_type" required>
                <?php foreach (leaveTypeOptions() as $opt): ?>
                    <option value="<?= e($opt) ?>"><?= e($opt) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Commutation</label>
            <select name="commutation">
                <option value="Requested">Requested</option>
                <option value="Not Requested">Not Requested</option>
            </select>
        </div>
        <div>
            <label>Start Date</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>
        <div>
            <label>End Date</label>
            <input type="date" name="end_date" id="end_date" required>
        </div>
        <div>
            <label>Total Days</label>
            <input type="number" name="days_display" id="total_days" step="0.5" readonly>
            <div class="inline-help">Auto-calculated based on the inclusive start and end dates.</div>
        </div>
        <div>
            <label>Available Balance</label>
            <input type="text" id="balance_display" value="Vacation: <?= e($user['vacation_leave']) ?> | Sick: <?= e($user['sick_leave']) ?>" readonly>
        </div>
        <div class="full">
            <label>Specific Details / Purpose</label>
            <textarea name="specific_details" rows="3" placeholder="Example: medical checkup, seminar attendance, family matter, etc."></textarea>
        </div>
        <div class="full">
            <label>Reason for Leave</label>
            <textarea name="reason" rows="5" required placeholder="State the reason for your leave request."></textarea>
        </div>
        <div class="full actions">
            <button type="submit">Submit Digital Leave Form</button>
            <a class="btn secondary" href="print_my_requests.php">Print History</a>
        </div>
    </form>
</div>
<script>
(function(){
  const s=document.getElementById('start_date');
  const e=document.getElementById('end_date');
  const out=document.getElementById('total_days');
  function calc(){
    if(!s.value||!e.value){out.value='';return;}
    const start=new Date(s.value+'T00:00:00');
    const end=new Date(e.value+'T00:00:00');
    const diff=Math.floor((end-start)/(1000*60*60*24))+1;
    out.value=diff>0?diff:'';
  }
  s.addEventListener('change',calc);e.addEventListener('change',calc);
})();
</script>
<?php include 'layout_bottom.php'; ?>