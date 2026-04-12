<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
if (isLoggedIn()) { header('Location: ' . APP_URL . '/pages/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $result   = loginUser($email, $password);
    if ($result['success']) {
        header('Location: ' . APP_URL . '/pages/dashboard.php');
        exit;
    }
    $error = $result['message'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="icon">💚</span>
            <h1>HealthTracker</h1>
            <p>Your daily wellness companion</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group" style="margin-bottom:16px">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="you@example.com"
                       value="<?= sanitize($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="margin-bottom:24px">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                Sign In →
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="<?= APP_URL ?>/pages/register.php">Sign up</a>
        </div>
        <div class="auth-footer" style="margin-top:8px;font-size:11px">
            Demo: demo@example.com / password
        </div>
    </div>
</div>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
