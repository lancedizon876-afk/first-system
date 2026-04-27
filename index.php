<?php
require_once __DIR__ . '/config.php';
if (isset($_SESSION['user'])) {
    require_once __DIR__ . '/auth.php';
    redirect(dashboardForRole($_SESSION['user']['role']));
}
redirect('login.php');
?>