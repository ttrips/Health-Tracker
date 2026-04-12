<?php
$page  = 'dashboard';
$title = 'Dashboard — ' . APP_NAME;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser();
$db   = getDB();
$uid  = $user['id'];
$today = date('Y-m-d');

// Today's calories
$stmt = $db->prepare('SELECT COALESCE(SUM(calories),0) as total FROM nutrition_logs WHERE user_id=? AND DATE(logged_at)=?');
$stmt->execute([$uid, $today]);
$calories_today = (int)$stmt->fetchColumn();

// Today's water ml
$stmt = $db->prepare('SELECT COALESCE(SUM(amount_ml),0) as total FROM water_logs WHERE user_id=? AND DATE(logged_at)=?');
$stmt->execute([$uid, $today]);
$water_today = (int)$stmt->fetchColumn();

// Today's workout minutes
$stmt = $db->prepare('SELECT COALESCE(SUM(duration),0) as total FROM workout_logs WHERE user_id=? AND DATE(logged_at)=?');
$stmt->execute([$uid, $today]);
$workout_today = (int)$stmt->fetchColumn();

// Latest mood
$stmt = $db->prepare('SELECT mood FROM mood_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 1');
$stmt->execute([$uid]);
$latest_mood = (int)($stmt->fetchColumn() ?: 0);

// Goals
$stmt = $db->prepare('SELECT * FROM goals WHERE user_id=?');
$stmt->execute([$uid]);
$goals = $stmt->fetch() ?: ['calorie_goal'=>2000,'water_goal_ml'=>2500,'workout_goal'=>30];

// Recent mood (last 7)
$stmt = $db->prepare('SELECT mood, DATE(logged_at) as day FROM mood_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 7');
$stmt->execute([$uid]);
$recent_moods = $stmt->fetchAll();

// Recent meals
$stmt = $db->prepare('SELECT * FROM nutrition_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 5');
$stmt->execute([$uid]);
$recent_meals = $stmt->fetchAll();

$mood_emoji = ['','😩','😞','😐','😊','🤩'];
$mood_label = ['','Terrible','Bad','Okay','Good','Great'];

$cal_pct     = min(100, $calories_today / max(1,$goals['calorie_goal']) * 100);
$water_pct   = min(100, $water_today   / max(1,$goals['water_goal_ml']) * 100);
$workout_pct = min(100, $workout_today / max(1,$goals['workout_goal'])  * 100);

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?>, <?= sanitize(explode(' ', $user['name'])[0]) ?> <?= $user['avatar'] ?></h1>
    <p class="page-subtitle"><?= date('l, F j, Y') ?> · Here's your health snapshot</p>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card" style="--accent: var(--amber)">
        <div class="stat-icon">🔥</div>
        <div class="stat-value"><?= number_format($calories_today) ?></div>
        <div class="stat-label">Calories Today</div>
        <div class="stat-sub">Goal: <?= number_format($goals['calorie_goal']) ?> kcal</div>
        <div class="progress-wrap">
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="background:var(--amber)" data-width="<?= round($cal_pct) ?>"></div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="--accent: var(--teal)">
        <div class="stat-icon">💧</div>
        <div class="stat-value"><?= $water_today ?>ml</div>
        <div class="stat-label">Water Intake</div>
        <div class="stat-sub">Goal: <?= $goals['water_goal_ml'] ?>ml</div>
        <div class="progress-wrap">
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="background:var(--teal)" data-width="<?= round($water_pct) ?>"></div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="--accent: var(--blue)">
        <div class="stat-icon">💪</div>
        <div class="stat-value"><?= $workout_today ?>min</div>
        <div class="stat-label">Workout Today</div>
        <div class="stat-sub">Goal: <?= $goals['workout_goal'] ?>min</div>
        <div class="progress-wrap">
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="background:var(--blue)" data-width="<?= round($workout_pct) ?>"></div>
            </div>
        </div>
    </div>

    <div class="stat-card" style="--accent: var(--green)">
        <div class="stat-icon"><?= $latest_mood ? $mood_emoji[$latest_mood] : '❓' ?></div>
        <div class="stat-value"><?= $latest_mood ? $mood_label[$latest_mood] : '—' ?></div>
        <div class="stat-label">Latest Mood</div>
        <div class="stat-sub"><a href="<?= APP_URL ?>/pages/mood.php" style="color:var(--green)">Log mood →</a></div>
    </div>
</div>

<!-- Two col -->
<div class="two-col">
    <!-- Recent meals -->
    <div class="card">
        <div class="card-title">Recent Meals</div>
        <?php if ($recent_meals): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Meal</th><th>Type</th><th>Kcal</th></tr></thead>
                <tbody>
                <?php foreach ($recent_meals as $m): ?>
                <tr>
                    <td><?= sanitize($m['meal_name']) ?></td>
                    <td><span class="badge badge-green"><?= $m['meal_type'] ?></span></td>
                    <td><?= $m['calories'] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:var(--muted);font-size:13px">No meals logged today. <a href="<?= APP_URL ?>/pages/nutrition.php" style="color:var(--green)">Add one →</a></p>
        <?php endif; ?>
    </div>

    <!-- Mood history -->
    <div class="card">
        <div class="card-title">Mood History (Last 7)</div>
        <?php if ($recent_moods): ?>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
            <?php foreach ($recent_moods as $m): ?>
                <div style="text-align:center">
                    <div style="font-size:26px"><?= $mood_emoji[$m['mood']] ?></div>
                    <div style="font-size:10px;color:var(--muted)"><?= date('M j', strtotime($m['day'])) ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:var(--muted);font-size:13px">No mood logs yet. <a href="<?= APP_URL ?>/pages/mood.php" style="color:var(--green)">Start tracking →</a></p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
