<?php
// report.php
require_once 'config.php';

$session_id = isset($_GET['session_id']) ? trim($_GET['session_id']) : '';
if ($session_id === '') {
    echo "No session_id provided. Open index.php and use the 'Missing Keys Report' link for your session.";
    exit;
}

// canonical list of keys to check (extend as needed)
$allKeys = [
    'Backquote','Digit1','Digit2','Digit3','Digit4','Digit5','Digit6','Digit7','Digit8','Digit9','Digit0','Minus','Equal','Backspace',
    'Tab','KeyQ','KeyW','KeyE','KeyR','KeyT','KeyY','KeyU','KeyI','KeyO','KeyP','BracketLeft','BracketRight','Backslash',
    'CapsLock','KeyA','KeyS','KeyD','KeyF','KeyG','KeyH','KeyJ','KeyK','KeyL','Semicolon','Quote','Enter',
    'ShiftLeft','KeyZ','KeyX','KeyC','KeyV','KeyB','KeyN','KeyM','Comma','Period','Slash','ShiftRight',
    'ControlLeft','MetaLeft','AltLeft','Space','AltRight','MetaRight','ContextMenu','ControlRight',
    // Multimedia / special
    'VolumeMute','VolumeDown','VolumeUp',
    'MediaTrackPrevious','MediaPlayPause','MediaTrackNext',
    'LaunchMail','LaunchApp1','LaunchApp2','LaunchCalculator',
    'BrightnessDown','BrightnessUp','PrintScreen','ScrollLock','Pause'
];

// Get distinct detected keys for session
$stmt = $pdo->prepare("SELECT DISTINCT key_code FROM key_logs WHERE session_id = ?");
$stmt->execute([ $session_id ]);
$detected = $stmt->fetchAll(PDO::FETCH_COLUMN);
$detected_map = array_flip($detected);

// Optional: fetch counts per code (for showing how many times pressed)
$countStmt = $pdo->prepare("SELECT key_code, COUNT(*) AS cnt FROM key_logs WHERE session_id = ? GROUP BY key_code");
$countStmt->execute([$session_id]);
$counts = $countStmt->fetchAll();
$counts_map = [];
foreach ($counts as $r) $counts_map[$r['key_code']] = $r['cnt'];
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Missing Keys Report — <?= htmlspecialchars($session_id) ?></title>
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background: #f4f6fb;
        padding: 18px
    }

    .box {
        background: #fff;
        padding: 16px;
        border-radius: 8px;
        max-width: 900px;
        margin: 0 auto;
        border: 1px solid #e6eef8
    }

    h1 {
        margin: 0 0 6px
    }

    .legend {
        color: #555;
        font-size: 14px;
        margin-bottom: 12px
    }

    ul.checklist {
        list-style: none;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px
    }

    ul.checklist li {
        min-width: 220px;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #eee;
        background: #fafafa
    }

    .ok {
        border-color: #10b981;
        background: #ecfdf5
    }

    .missing {
        border-color: #f87171;
        background: #fff5f5
    }

    .count {
        float: right;
        color: #334155;
        font-weight: 600
    }

    .actions {
        margin-top: 12px
    }

    a.button {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 6px;
        background: #2563eb;
        color: #fff;
        text-decoration: none
    }
    </style>
</head>

<body>
    <div class="box">
        <h1>Missing Keys Report</h1>
        <div class="legend">Session: <strong><?= htmlspecialchars($session_id) ?></strong> — total detected:
            <strong><?= count($detected) ?></strong></div>

        <div class="actions">
            <a class="button" href="index.php" target="_blank">Back to Tester</a>
            &nbsp;

        </div>

        <ul class="checklist" style="margin-top:12px">
            <?php foreach ($allKeys as $code): 
         $is = isset($detected_map[$code]);
      ?>
            <li class="<?= $is ? 'ok' : 'missing' ?>">
                <?php if ($is): ?>
                ✔ <?= htmlspecialchars($code) ?>
                <span class="count"><?= isset($counts_map[$code]) ? $counts_map[$code] : '1' ?></span>
                <?php else: ?>
                ✘ <?= htmlspecialchars($code) ?>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <div style="margin-top:12px;color:#555">
            <strong>Notes:</strong>
            <ul>
                <li>Browsers and OSes differ in which multimedia keys are exposed to web pages. If a key is not marked
                    OK, try another browser or make sure the key is not captured by the OS or another app.</li>
                <li>You can expand the canonical list in <code>report.php</code> to include more codes.</li>
            </ul>
        </div>
    </div>
</body>

</html>