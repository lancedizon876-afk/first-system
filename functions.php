<?php
require_once __DIR__ . '/config.php';

function addNotification(PDO $db, int $userId, string $message): void {
    $stmt = $db->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$userId, $message]);
}

function addAudit(PDO $db, ?int $userId, string $action, string $details = ''): void {
    $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $action, $details]);
}

function notifyRole(PDO $db, string $role, string $message): void {
    $stmt = $db->prepare("SELECT id FROM users WHERE role = ?");
    $stmt->execute([$role]);
    foreach ($stmt->fetchAll() as $row) {
        addNotification($db, (int)$row['id'], $message);
    }
}

function leaveBalanceColumn(string $leaveType): string {
    return $leaveType === 'Vacation' ? 'vacation_leave' : 'sick_leave';
}

function roleLabel(string $role): string {
    switch ($role) {
        case 'admin': return 'Admin / HRMO';
        case 'head': return 'Head';
        case 'teaching': return 'Teaching Personnel';
        case 'non_teaching': return 'Non-Teaching Personnel';
        default: return ucfirst($role);
    }
}

function leaveTypeOptions(): array {
    return ['Vacation', 'Sick', 'Study', 'Others'];
}

function calculateInclusiveDays(string $startDate, string $endDate): float {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    if ($end < $start) return 0.0;
    return (float)$start->diff($end)->days + 1;
}

function statusBadgeClass(string $status): string {
    if (stripos($status, 'approved') !== false) return 'approved';
    if (stripos($status, 'reject') !== false) return 'rejected';
    if (stripos($status, 'review') !== false || stripos($status, 'pending') !== false) return 'pending';
    return 'review';
}
