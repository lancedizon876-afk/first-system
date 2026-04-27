<?php
require_once __DIR__ . '/auth.php';
requireLogin();
redirect(dashboardForRole(currentUser()['role']));
?>