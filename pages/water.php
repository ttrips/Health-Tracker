<?php
$page  = 'water';
$title = 'Water — ' . APP_NAME;
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
        $amount = (int)($_POST['amount_ml'] ?? 250);
        if ($amount < 1 || $amount > 2000) $err = 'Amount must be between 1ml and 2000ml.';
        else {
            $db->prepare('INSERT INTO water_logs (user_id, amount_ml) VALUES (?,?)')->execute([$uid, $amount]);
            $msg = "+{$amount}ml logged!";
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM water_logs WHERE id=? AND user_id=?')->execute([$id, $uid]);
        $msg = 'Entry deleted.';
    }
}

$today = date('Y-m-d');
$stmt  = $db->prepare('SELECT COALESCE(SUM(amount_ml),0) FROM water_logs WHERE user_id=? AND DATE(logged_at)=?');
$stmt->execute([$uid, $today]);
$today_ml = (int)$stmt->fetchColumn();

$stmt = $db->prepare('SELECT * FROM goals WHERE user_id=?');
$stmt->execute([$uid]);
$goal = $stmt->fetch() ?: ['water_goal_ml' => 2500];
$goal_ml = (int)$goal['water_goal_ml'];

$pct = min(100, $today_ml / max(1,$goal_ml) * 100);
$cups_filled = min(8, (int)round($today_ml / ($goal_ml / 8)));

$stmt = $db->prepare('SELECT * FROM water_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 20');
$stmt->execute([$uid]);
$logs = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">💧 Water Intake</h1>
    <p class="page-subtitle">Stay hydrated throughout the day</p>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= sanitize($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= sanitize($err) ?></div><?php endif; ?>

<div class="two-col">
    <div class="card">
        <div class="card-title">Today — <?= $today_ml ?>ml / <?= $goal_ml ?>ml</div>

        <!-- Visual cups -->
        <div class="water-grid" style="margin-bottom:20px">
            <?php for ($i = 0; $i < 8; $i++): ?>
            <div class="water-cup <?= $i < $cups_filled ? 'filled' : '' ?>">💧</div>
            <?php endfor; ?>
        </div>

        <div class="progress-bar-bg" style="height:12px;margin-bottom:20px">
            <div class="progress-bar-fill" style="background:var(--teal)" data-width="<?= round($pct) ?>"></div>
        </div>
        <p style="color:var(--muted);font-size:13px;margin-bottom:20px"><?= round($pct) ?>% of daily goal</p>

        <!-- Quick add buttons -->
        <div class="card-title">Quick Add</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
            <?php foreach ([150, 250, 350, 500] as $ml): ?>
            <form method="POST">
                <input type="hidden" name="action" value="log">
                <input type="hidden" name="amount_ml" value="<?= $ml ?>">
                <button type="submit" class="btn btn-ghost">+<?= $ml ?>ml</button>
            </form>
            <?php endforeach; ?>
        </div>

        <!-- Custom amount -->
        <div class="card-title">Custom Amount</div>
        <form method="POST" style="display:flex;gap:10px;align-items:flex-end">
            <input type="hidden" name="action" value="log">
            <div class="form-group" style="flex:1">
                <label>Amount (ml)</label>
                <input type="number" name="amount_ml" class="form-control" min="1" max="2000" placeholder="250">
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>

    <div class="card">
        <div class="card-title">Water Log</div>
        <?php if ($logs): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Amount</th><th>Time</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><span class="badge badge-teal">💧 <?= $l['amount_ml'] ?>ml</span></td>
                    <td style="color:var(--muted);font-size:12px"><?= date('M j, g:ia', strtotime($l['logged_at'])) ?></td>
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
            <p style="color:var(--muted);font-size:13px">No water logged today.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
