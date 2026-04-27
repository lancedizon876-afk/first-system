<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';
$user = currentUser();
$remark = trim($_POST['remark'] ?? '');

$stmt = $db->prepare("SELECT * FROM leave_requests WHERE id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch();
if (!$request) {
    flash('error', 'Request not found.');
    redirect('leave_requests.php');
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$request['employee_id']]);
$employee = $stmt->fetch();

if ($user['role'] === 'admin') {
    if ($request['status'] !== 'Pending Admin Review') {
        flash('error', 'This request is no longer pending admin review.');
        redirect('leave_requests.php');
    }

    if ($action === 'endorse') {
        $stmt = $db->prepare("UPDATE leave_requests SET status='Pending Head Approval', admin_action_by=?, admin_action_at=NOW(), admin_remark=? WHERE id=?");
        $stmt->execute([$user['id'], $remark ?: 'Endorsed by Admin', $id]);
        addNotification($db, (int)$employee['id'], 'Your leave request was endorsed by Admin and sent to Head.');
        notifyRole($db, 'head', 'A leave request from ' . $employee['fullname'] . ' is pending final approval.');
        addAudit($db, $user['id'], 'endorse_request', 'Admin endorsed request #' . $id);
        flash('success', 'Request endorsed to Head.');
    } elseif ($action === 'reject_admin') {
        $stmt = $db->prepare("UPDATE leave_requests SET status='Rejected by Admin', admin_action_by=?, admin_action_at=NOW(), admin_remark=? WHERE id=?");
        $stmt->execute([$user['id'], $remark ?: 'Rejected by Admin', $id]);
        addNotification($db, (int)$employee['id'], 'Your leave request was rejected by Admin.');
        addAudit($db, $user['id'], 'reject_request_admin', 'Admin rejected request #' . $id);
        flash('success', 'Request rejected.');
    }
    redirect('leave_requests.php');
}

if ($user['role'] === 'head') {
    if ($request['status'] !== 'Pending Head Approval') {
        flash('error', 'This request is not waiting for final approval.');
        redirect('leave_requests.php');
    }

    if ($action === 'approve') {
        $db->beginTransaction();
        try {
            $stmt = $db->prepare("UPDATE leave_requests SET status='Approved', head_action_by=?, head_action_at=NOW(), head_remark=? WHERE id=?");
            $stmt->execute([$user['id'], $remark ?: 'Approved by Head', $id]);

            if (in_array($request['leave_type'], ['Vacation', 'Sick'], true)) {
                $column = $request['leave_type'] === 'Vacation' ? 'vacation_leave' : 'sick_leave';
                $stmt = $db->prepare("UPDATE users SET {$column} = GREATEST(0, {$column} - ?) WHERE id = ?");
                $stmt->execute([$request['days'], $request['employee_id']]);
            }

            addNotification($db, (int)$employee['id'], 'Your leave request was approved and your balance was updated when applicable.');
            addAudit($db, $user['id'], 'approve_request_head', 'Head approved request #' . $id);
            $db->commit();
            flash('success', 'Request approved.');
        } catch (Throwable $e) {
            $db->rollBack();
            flash('error', 'Approval failed: ' . $e->getMessage());
        }
    } elseif ($action === 'reject_head') {
        $stmt = $db->prepare("UPDATE leave_requests SET status='Rejected by Head', head_action_by=?, head_action_at=NOW(), head_remark=? WHERE id=?");
        $stmt->execute([$user['id'], $remark ?: 'Rejected by Head', $id]);
        addNotification($db, (int)$employee['id'], 'Your leave request was rejected by Head.');
        addAudit($db, $user['id'], 'reject_request_head', 'Head rejected request #' . $id);
        flash('success', 'Request rejected.');
    }
    redirect('leave_requests.php');
}

http_response_code(403);
die('Access denied.');
?>