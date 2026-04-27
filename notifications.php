<?php
require_once __DIR__ . '/auth.php';
requireLogin();
$user = currentUser();

$stmt = $db->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();

$db->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$user['id']]);

$pageTitle = 'Notifications';
include 'layout_top.php';
?>
<div class="card">
    <h1>Notifications</h1>
    <?php if ($rows): ?>
        <ul class="list">
        <?php foreach ($rows as $row): ?>
            <li><?= e($row['message']) ?> <small class="muted">(<?= e($row['created_at']) ?>)</small></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="muted">No notifications yet.</p>
    <?php endif; ?>
</div>
<?php include 'layout_bottom.php'; ?>