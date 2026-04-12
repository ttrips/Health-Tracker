<?php
$page  = 'mood';
$title = 'Mood — ' . APP_NAME;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$user = currentUser();
$db   = getDB();
$uid  = $user['id'];

$msg = $err = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'log') {
        $mood = (int)($_POST['mood'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        if ($mood < 1 || $mood > 5) {
            $err = 'Please select a mood.';
        } else {
            $db->prepare('INSERT INTO mood_logs (user_id, mood, note) VALUES (?,?,?)')->execute([$uid, $mood, $note]);
            $msg = 'Mood logged!';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM mood_logs WHERE id=? AND user_id=?')->execute([$id, $uid]);
        $msg = 'Entry deleted.';
    }
}

// Fetch logs
$stmt = $db->prepare('SELECT * FROM mood_logs WHERE user_id=? ORDER BY logged_at DESC LIMIT 30');
$stmt->execute([$uid]);
$logs = $stmt->fetchAll();

$mood_emoji = ['','😩','😞','😐','😊','🤩'];
$mood_label = ['','Terrible','Bad','Okay','Good','Great'];
$mood_badge = ['','badge-red','badge-red','badge-amber','badge-green','badge-green'];

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">😊 Mood Tracker</h1>
    <p class="page-subtitle">How are you feeling today?</p>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= sanitize($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= sanitize($err) ?></div><?php endif; ?>

<div class="two-col">
    <!-- Log form -->
    <div class="card">
        <div class="card-title">Log Your Mood</div>
        <form method="POST">
            <input type="hidden" name="action" value="log">
            <input type="hidden" name="mood" id="mood-input" value="">

            <div class="mood-grid">
                <?php foreach ([1,2,3,4,5] as $v): ?>
                <button type="button" class="mood-btn" data-value="<?= $v ?>">
                    <span class="mood-emoji"><?= $mood_emoji[$v] ?></span>
                    <span class="mood-label"><?= $mood_label[$v] ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <div class="form-group" style="margin-bottom:16px">
                <label>Note (optional)</label>
                <textarea name="note" class="form-control" rows="3" placeholder="What's on your mind?"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Log Mood</button>
        </form>
    </div>

    <!-- History -->
    <div class="card">
        <div class="card-title">Mood History</div>
        <?php if ($logs): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Mood</th><th>Note</th><th>Date</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td>
                        <span class="badge <?= $mood_badge[$l['mood']] ?>">
                            <?= $mood_emoji[$l['mood']] ?> <?= $mood_label[$l['mood']] ?>
                        </span>
                    </td>
                    <td style="color:var(--muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        <?= sanitize($l['note'] ?: '—') ?>
                    </td>
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
            <p style="color:var(--muted);font-size:13px">No mood logs yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
