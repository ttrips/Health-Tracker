<?php
require_once __DIR__ . '/../includes/auth.php';
startSession();
if (isLoggedIn()) { header('Location: ' . APP_URL . '/pages/dashboard.php'); exit; }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $result   = registerUser($name, $email, $password);
    if ($result['success']) {
        loginUser($email, $password);
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
    <title>Register — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="icon">💚</span>
            <h1>Create Account</h1>
            <p>Start tracking your health today</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group" style="margin-bottom:16px">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control"
                       placeholder="Your name"
                       value="<?= sanitize($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="margin-bottom:16px">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       placeholder="you@example.com"
                       value="<?= sanitize($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group" style="margin-bottom:24px">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 characters" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                Create Account →
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="<?= APP_URL ?>/pages/login.php">Sign in</a>
        </div>
    </div>
</div>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
