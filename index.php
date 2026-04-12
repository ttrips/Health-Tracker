<?php
require_once __DIR__ . '/includes/auth.php';
startSession();
if (isLoggedIn()) {
    header('Location: ' . APP_URL . '/pages/dashboard.php');
} else {
    header('Location: ' . APP_URL . '/pages/login.php');
}
exit;
