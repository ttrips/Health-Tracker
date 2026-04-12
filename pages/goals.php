<?php
$page  = 'goals';
$title = 'Goals — ' . APP_NAME;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser();
$db   = getDB();
$uid  = $user['id'];

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cal     = (int)($_POST['calorie_goal'] ?? 2000);
    $water   = (int)($_POST['water_goal_ml'] ?? 2500);
    $workout = (int)($_POST['workout_goal'] ?? 30);

    if ($cal < 500 || $cal > 10000)   $err = 'Calorie goal must be 500–10000.';
    elseif ($water < 500 || $water > 10000) $err = 'Water goal must be 500–10000ml.';
    elseif ($workout < 5 || $workout > 300)  $err = 'Workout goal must be 5–300 minutes.';
    else {
        $db->prepare('INSERT INTO goals (user_id,calorie_goal,water_goal_ml,workout_goal) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE calorie_goal=VALUES(calorie_goal),water_goal_ml=VALUES(water_goal_ml),workout_goal=VALUES(workout_goal)')
           ->execute([$uid, $cal, $water, $workout]);
        $msg = 'Goals updated!';
    }
}

$stmt = $db->prepare('SELECT * FROM goals WHERE user_id=?');
$stmt->execute([$uid]);
$goals = $stmt->fetch() ?: ['calorie_goal'=>2000,'water_goal_ml'=>2500,'workout_goal'=>30];

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">🎯 Daily Goals</h1>
    <p class="page-subtitle">Set your targets and stay on track</p>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= sanitize($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= sanitize($err) ?></div><?php endif; ?>

<div style="max-width:540px">
    <div class="card">
        <div class="card-title">Update Goals</div>
        <form method="POST">
            <div class="form-group" style="margin-bottom:20px">
                <label>🔥 Daily Calorie Goal (kcal)</label>
                <input type="number" name="calorie_goal" class="form-control"
                       min="500" max="10000"
                       value="<?= (int)$goals['calorie_goal'] ?>">
                <small style="color:var(--muted);font-size:12px">Typical range: 1500–3000 kcal</small>
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label>💧 Daily Water Goal (ml)</label>
                <input type="number" name="water_goal_ml" class="form-control"
                       min="500" max="10000"
                       value="<?= (int)$goals['water_goal_ml'] ?>">
                <small style="color:var(--muted);font-size:12px">Typical: 2000–3000ml (8–12 cups)</small>
            </div>
            <div class="form-group" style="margin-bottom:24px">
                <label>💪 Daily Workout Goal (minutes)</label>
                <input type="number" name="workout_goal" class="form-control"
                       min="5" max="300"
                       value="<?= (int)$goals['workout_goal'] ?>">
                <small style="color:var(--muted);font-size:12px">WHO recommends 30+ min/day</small>
            </div>
            <button type="submit" class="btn btn-primary">Save Goals</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
