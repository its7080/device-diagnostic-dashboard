<?php
require_once 'config.php';

$session_id = isset($_GET['session_id']) ? trim($_GET['session_id']) : '';
if ($session_id === '') {
    echo "No session_id provided. Open index.php and use the Missing Keys Report link for your session.";
    exit;
}

$allKeys = [
    'Escape','F1','F2','F3','F4','F5','F6','F7','F8','F9','F10','F11','F12',
    'PrintScreen','ScrollLock','Pause','MediaTrackPrevious','MediaPlayPause','MediaTrackNext','VolumeMute','VolumeDown','VolumeUp',
    'Backquote','Digit1','Digit2','Digit3','Digit4','Digit5','Digit6','Digit7','Digit8','Digit9','Digit0','Minus','Equal','Backspace',
    'Insert','Home','PageUp','Delete','End','PageDown',
    'Tab','KeyQ','KeyW','KeyE','KeyR','KeyT','KeyY','KeyU','KeyI','KeyO','KeyP','BracketLeft','BracketRight','Backslash',
    'CapsLock','KeyA','KeyS','KeyD','KeyF','KeyG','KeyH','KeyJ','KeyK','KeyL','Semicolon','Quote','Enter',
    'ShiftLeft','KeyZ','KeyX','KeyC','KeyV','KeyB','KeyN','KeyM','Comma','Period','Slash','ShiftRight',
    'ControlLeft','MetaLeft','AltLeft','Space','AltRight','MetaRight','ContextMenu','ControlRight',
    'ArrowLeft','ArrowDown','ArrowRight','ArrowUp',
    'NumLock','NumpadDivide','NumpadMultiply','NumpadSubtract','Numpad7','Numpad8','Numpad9','NumpadAdd','Numpad4','Numpad5','Numpad6','Numpad1','Numpad2','Numpad3','Numpad0','NumpadDecimal','NumpadEnter',
    'LaunchMail','LaunchCalculator','MouseLeft','MouseMiddle','MouseRight'
];

$stmt = $pdo->prepare("SELECT key_code, COUNT(*) AS cnt FROM key_logs WHERE session_id = ? GROUP BY key_code");
$stmt->execute([$session_id]);
$rows = $stmt->fetchAll();
$counts = [];
foreach ($rows as $r) $counts[$r['key_code']] = (int)$r['cnt'];
$detectedCount = count($counts);
$total = count($allKeys);
$coverage = $total > 0 ? round(($detectedCount / $total) * 100, 1) : 0;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Diagnostic Report — <?= htmlspecialchars($session_id) ?></title>
<style>
body { font-family: Inter, system-ui, Arial, sans-serif; margin: 0; background: #eef2ff; color: #111827; padding: 20px; }
.box { max-width: 980px; margin: 0 auto; background: #fff; border:1px solid #dbeafe; border-radius: 14px; padding: 18px; }
.grid { display:grid; grid-template-columns: repeat(3,minmax(140px,1fr)); gap:10px; }
.stat { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; }
ul { list-style:none; padding:0; display:grid; grid-template-columns: repeat(auto-fill,minmax(220px,1fr)); gap:8px; }
li { border-radius:8px; padding:10px; border:1px solid #e5e7eb; background:#fafafa; }
.ok { background:#ecfdf5; border-color:#10b981; }
.missing { background:#fef2f2; border-color:#ef4444; }
.count { float:right; font-weight:700; color:#334155; }
.badge { display:inline-block; border-radius:999px; padding:4px 8px; background:#dbeafe; color:#1d4ed8; font-weight:700; }
</style>
</head>
<body>
<div class="box">
<h1>Keyboard & Mouse Diagnostic Report</h1>
<p>Session ID: <strong><?= htmlspecialchars($session_id) ?></strong></p>
<div class="grid">
    <div class="stat"><div>Total mapped keys</div><strong><?= $total ?></strong></div>
    <div class="stat"><div>Detected keys</div><strong><?= $detectedCount ?></strong></div>
    <div class="stat"><div>Coverage</div><strong><?= $coverage ?>%</strong></div>
</div>
<p><span class="badge">Tip</span> Multimedia keys can be blocked by OS/browser shortcuts.</p>
<ul>
<?php foreach ($allKeys as $code): $ok = isset($counts[$code]); ?>
<li class="<?= $ok ? 'ok' : 'missing' ?>"><?= $ok ? '✔' : '✘' ?> <?= htmlspecialchars($code) ?><?php if ($ok): ?><span class="count"><?= $counts[$code] ?></span><?php endif; ?></li>
<?php endforeach; ?>
</ul>
</div>
</body>
</html>

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