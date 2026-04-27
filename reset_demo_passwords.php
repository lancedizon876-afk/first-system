<?php
require_once __DIR__ . '/config.php';

$users = [
    'admin@isu.local' => '123456',
    'head@isu.local' => '123456',
    'teaching@isu.local' => '123456',
    'nonteaching@isu.local' => '123456',
];

echo "<h2>Reset Demo Passwords</h2>";

try {
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
    foreach ($users as $email => $plainPassword) {
        $stmt->execute([password_hash($plainPassword, PASSWORD_DEFAULT), $email]);
        echo "<p>Updated: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>";
    }
    echo "<p><strong>Done.</strong> You can now log in using password <strong>123456</strong>.</p>";
    echo "<p><a href='login.php'>Go to Login</a></p>";
} catch (Throwable $e) {
    echo "<p style='color:red;'><strong>Reset failed:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>