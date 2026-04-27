<?php
require_once __DIR__ . '/config.php';
session_destroy();
session_start();
flash('success', 'Logged out successfully.');
redirect('login.php');
?>