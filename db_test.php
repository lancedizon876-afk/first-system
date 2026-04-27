<?php
require_once __DIR__ . '/config.php';

echo "<h2>Database Connection Test</h2>";
try {
    $count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "<p style='color:green;'>Connected successfully.</p>";
    echo "<p>Users found: <strong>" . (int)$count . "</strong></p>";
    $rows = $db->query("SELECT email, role FROM users ORDER BY id")->fetchAll();
    echo "<ul>";
    foreach ($rows as $row) {
        echo "<li>" . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . " — " . htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8') . "</li>";
    }
    echo "</ul>";
} catch (Throwable $e) {
    echo "<p style='color:red;'><strong>Connection failed:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>