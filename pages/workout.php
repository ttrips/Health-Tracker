<?php
$page  = 'workout';
$title = 'Workout — ' . APP_NAME;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser();
$db   = getDB();
$uid  = $user['id'];

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'log') {
        $exercise = trim($_POST['exercise'] ?? '');
        $duration = (int)($_POST['duration'] ?? 0);
        $intensity = $_POST['intensity'] ?? 'medium';
        $burned   = (int)($_POST['calories_burned'] ?? 0);
        $notes    = trim($_POST['notes'] ?? '');

        if (!$exercise)     $err = 'Exercise name required.';
        elseif ($duration < 1) $err = 'Duration must be at least 1 minute.';
        else {
            $db->prepare('INSERT INTO workout_logs (user_id,exercise,duration,intensity,calories_burned,notes) VALUES (?,?,?,?,?,?)')
               ->execute([$uid, $exercise, $duration, $intensity, $burned, $notes]);
            $msg = 'Workout logged!';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM workout_logs WHERE id=? AND user_id=?')->execute([$id, $uid]);
        $msg = 'Entry deleted.';
    }
}

// Stats
$today = date('Y-m-d');
$stmt  = $db->prepare('SELECT COALESCE(SUM(duration),0) mins, COALESCE(SUM(calories_burned),0) burned FROM workout_logs WHERE user_id=? AND DATE(logged_at)=?');
$stmt->execute([$uid, $today]);
$today_stats = $stmt->fetch();

$stmt = $db->prepare('SELECT * FROM workout_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 30');
$stmt->execute([$uid]);
$logs = $stmt->fetchAll();

$int_badge = ['low'=>'badge-green','medium'=>'badge-amber','high'=>'badge-red'];

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">💪 Workout Tracker</h1>
    <p class="page-subtitle">Log your exercises and stay active</p>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= sanitize($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= sanitize($err) ?></div><?php endif; ?>

<div class="stats-grid" style="grid-template-columns:repeat(2,1fr);max-width:400px">
    <div class="stat-card" style="--accent:var(--blue)">
        <div class="stat-icon">⏱️</div>
        <div class="stat-value"><?= $today_stats['mins'] ?></div>
        <div class="stat-label">Minutes Today</div>
    </div>
    <div class="stat-card" style="--accent:var(--red)">
        <div class="stat-icon">🔥</div>
        <div class="stat-value"><?= $today_stats['burned'] ?></div>
        <div class="stat-label">Calories Burned</div>
    </div>
</div>

<div class="two-col">
    <div class="card">
        <div class="card-title">Log Workout</div>
        <form method="POST">
            <input type="hidden" name="action" value="log">
            <div class="form-grid">
                <div class="form-group" style="grid-column:1/-1">
                    <label>Exercise</label>
                    <input type="text" name="exercise" class="form-control" placeholder="e.g. Running, Push-ups" required>
                </div>
                <div class="form-group">
                    <label>Duration (minutes)</label>
                    <input type="number" name="duration" class="form-control" min="1" max="300" placeholder="30" required>
                </div>
                <div class="form-group">
                    <label>Intensity</label>
                    <select name="intensity" class="form-control">
                        <option value="low">🟢 Low</option>
                        <option value="medium" selected>🟡 Medium</option>
                        <option value="high">🔴 High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Calories Burned</label>
                    <input type="number" name="calories_burned" class="form-control" min="0" placeholder="200">
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Log Workout</button>
        </form>
    </div>

    <div class="card">
        <div class="card-title">Workout History</div>
        <?php if ($logs): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Exercise</th><th>Duration</th><th>Intensity</th><th>Date</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= sanitize($l['exercise']) ?></td>
                    <td><?= $l['duration'] ?>min</td>
                    <td><span class="badge <?= $int_badge[$l['intensity']] ?>"><?= $l['intensity'] ?></span></td>
                    <td style="color:var(--muted);font-size:12px"><?= date('M j', strtotime($l['logged_at'])) ?></td>
                    <td>
                        <form method="POST" class="delete-form">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">✕</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p style="color:var(--muted);font-size:13px">No workouts logged yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
