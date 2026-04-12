<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser();
$page = $page ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($title ?? APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">💚</span>
            <span class="brand-name">HealthTracker</span>
        </div>

        <ul class="nav-links">
            <li><a href="<?= APP_URL ?>/pages/dashboard.php" class="nav-link <?= $page==='dashboard' ? 'active':'' ?>">
                <span class="nav-icon">⚡</span> Dashboard
            </a></li>
            <li><a href="<?= APP_URL ?>/pages/mood.php" class="nav-link <?= $page==='mood' ? 'active':'' ?>">
                <span class="nav-icon">😊</span> Mood
            </a></li>
            <li><a href="<?= APP_URL ?>/pages/nutrition.php" class="nav-link <?= $page==='nutrition' ? 'active':'' ?>">
                <span class="nav-icon">🥗</span> Nutrition
            </a></li>
            <li><a href="<?= APP_URL ?>/pages/workout.php" class="nav-link <?= $page==='workout' ? 'active':'' ?>">
                <span class="nav-icon">💪</span> Workout
            </a></li>
            <li><a href="<?= APP_URL ?>/pages/water.php" class="nav-link <?= $page==='water' ? 'active':'' ?>">
                <span class="nav-icon">💧</span> Water
            </a></li>
            <li><a href="<?= APP_URL ?>/pages/goals.php" class="nav-link <?= $page==='goals' ? 'active':'' ?>">
                <span class="nav-icon">🎯</span> Goals
            </a></li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-chip">
                <span class="user-avatar"><?= $user['avatar'] ?></span>
                <div class="user-info">
                    <span class="user-name"><?= sanitize($user['name']) ?></span>
                    <span class="user-email"><?= sanitize($user['email']) ?></span>
                </div>
            </div>
            <a href="<?= APP_URL ?>/pages/logout.php" class="logout-btn">↩ Logout</a>
        </div>
    </nav>

    <!-- Main content -->
    <main class="main-content">
