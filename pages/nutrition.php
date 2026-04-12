<?php
$page  = 'nutrition';
$title = 'Nutrition — ' . APP_NAME;
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
        $meal     = trim($_POST['meal_name'] ?? '');
        $calories = (int)($_POST['calories'] ?? 0);
        $protein  = (float)($_POST['protein']  ?? 0);
        $carbs    = (float)($_POST['carbs']    ?? 0);
        $fats     = (float)($_POST['fats']     ?? 0);
        $type     = $_POST['meal_type'] ?? 'snack';

        if (!$meal)          $err = 'Meal name is required.';
        elseif ($calories < 0) $err = 'Calories cannot be negative.';
        else {
            $db->prepare('INSERT INTO nutrition_logs (user_id,meal_name,calories,protein,carbs,fats,meal_type) VALUES (?,?,?,?,?,?,?)')
               ->execute([$uid, $meal, $calories, $protein, $carbs, $fats, $type]);
            $msg = 'Meal logged!';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM nutrition_logs WHERE id=? AND user_id=?')->execute([$id, $uid]);
        $msg = 'Entry deleted.';
    }
}

// Today summary
$today = date('Y-m-d');
$stmt  = $db->prepare('SELECT COALESCE(SUM(calories),0) cal, COALESCE(SUM(protein),0) pro, COALESCE(SUM(carbs),0) carb, COALESCE(SUM(fats),0) fat FROM nutrition_logs WHERE user_id=? AND DATE(logged_at)=?');
$stmt->execute([$uid, $today]);
$summary = $stmt->fetch();

// All logs
$stmt = $db->prepare('SELECT * FROM nutrition_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 50');
$stmt->execute([$uid]);
$logs = $stmt->fetchAll();

$type_badge = ['breakfast'=>'badge-amber','lunch'=>'badge-green','dinner'=>'badge-blue','snack'=>'badge-teal'];

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">🥗 Nutrition Tracker</h1>
    <p class="page-subtitle">Track what you eat and stay on target</p>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= sanitize($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= sanitize($err) ?></div><?php endif; ?>

<!-- Today's macros -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
    <?php
    $macros = [
        ['🔥','Calories', round($summary['cal']), 'kcal', 'amber'],
        ['🥩','Protein',  round($summary['pro']),  'g',    'red'],
        ['🍞','Carbs',    round($summary['carb']), 'g',    'amber'],
        ['🧈','Fats',     round($summary['fat']),  'g',    'blue'],
    ];
    foreach ($macros as [$ic,$lb,$val,$unit,$col]): ?>
    <div class="stat-card" style="--accent:var(--<?= $col ?>)">
        <div class="stat-icon"><?= $ic ?></div>
        <div class="stat-value"><?= $val ?></div>
        <div class="stat-label"><?= $lb ?> today</div>
        <div class="stat-sub"><?= $unit ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="two-col">
    <!-- Log form -->
    <div class="card">
        <div class="card-title">Add Meal</div>
        <form method="POST">
            <input type="hidden" name="action" value="log">
            <div class="form-grid">
                <div class="form-group" style="grid-column:1/-1">
                    <label>Meal Name</label>
                    <input type="text" name="meal_name" class="form-control" placeholder="e.g. Chicken rice bowl" required>
                </div>
                <div class="form-group">
                    <label>Meal Type</label>
                    <select name="meal_type" class="form-control">
                        <option value="breakfast">🌅 Breakfast</option>
                        <option value="lunch">☀️ Lunch</option>
                        <option value="dinner">🌙 Dinner</option>
                        <option value="snack" selected>🍎 Snack</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Calories (kcal)</label>
                    <input type="number" name="calories" class="form-control" min="0" max="5000" placeholder="350">
                </div>
                <div class="form-group">
                    <label>Protein (g)</label>
                    <input type="number" name="protein" class="form-control" min="0" step="0.1" placeholder="25">
                </div>
                <div class="form-group">
                    <label>Carbs (g)</label>
                    <input type="number" name="carbs" class="form-control" min="0" step="0.1" placeholder="45">
                </div>
                <div class="form-group">
                    <label>Fats (g)</label>
                    <input type="number" name="fats" class="form-control" min="0" step="0.1" placeholder="12">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Meal</button>
        </form>
    </div>

    <!-- Log -->
    <div class="card">
        <div class="card-title">Meal Log</div>
        <?php if ($logs): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Meal</th><th>Type</th><th>Kcal</th><th>Date</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= sanitize($l['meal_name']) ?></td>
                    <td><span class="badge <?= $type_badge[$l['meal_type']] ?>"><?= $l['meal_type'] ?></span></td>
                    <td><?= $l['calories'] ?></td>
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
            <p style="color:var(--muted);font-size:13px">No meals logged yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
