<?php
session_start();
date_default_timezone_set('Asia/Manila');

define('DB_HOST', 'localhost');
define('DB_NAME', 'isu_leave_system_modern');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect($url) {
    header("Location: " . $url);
    exit;
}
function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return;
    }
    if (isset($_SESSION['_flash'][$key])) {
        $msg = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }
    return null;
}
?>