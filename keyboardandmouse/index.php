<?php
require_once __DIR__ . '/../config.php';

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

$mode = isset($_GET['mode']) ? (string) $_GET['mode'] : 'ui';

if ($mode === 'log') {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        exit;
    }

    $data = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
        exit;
    }

    $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [$data];
    $stmt = $pdo->prepare('INSERT INTO key_logs (session_id, key_code, key_value, created_at) VALUES (?, ?, ?, NOW())');

    $inserted = 0;
    foreach ($items as $item) {
        $session = isset($item['session_id']) ? trim((string) $item['session_id']) : '';
        $code = isset($item['key_code']) ? trim((string) $item['key_code']) : '';
        $value = isset($item['key_value']) ? (string) $item['key_value'] : $code;
        if ($session === '' || $code === '') {
            continue;
        }
        $stmt->execute([$session, $code, $value]);
        $inserted++;
    }

    echo json_encode(['ok' => true, 'inserted' => $inserted]);
    exit;
}

if ($mode === 'report') {
    $sessionId = isset($_GET['session_id']) ? trim((string) $_GET['session_id']) : '';
    if ($sessionId === '') {
        echo 'No session_id provided. Open keyboardandmouse/index.php and use the Missing Keys Report link for your session.';
        exit;
    }

    $stmt = $pdo->prepare('SELECT key_code, COUNT(*) AS cnt FROM key_logs WHERE session_id = ? GROUP BY key_code');
    $stmt->execute([$sessionId]);
    $rows = $stmt->fetchAll();

    $counts = [];
    foreach ($rows as $row) {
        $counts[$row['key_code']] = (int) $row['cnt'];
    }

    $detected = array_keys($counts);
    $detectedMap = array_fill_keys($detected, true);
    $detectedCount = count($detected);
    $total = count($allKeys);
    $coverage = $total > 0 ? round(($detectedCount / $total) * 100, 1) : 0;
    ?>
    <!doctype html>
    <html lang="en">
    <head><meta charset="utf-8"><title>Missing Keys Report — <?= htmlspecialchars($sessionId) ?></title>
    <style>body{font-family:Inter,system-ui,Arial,sans-serif;margin:0;background:#eef2ff;color:#111827;padding:20px}.box{max-width:980px;margin:0 auto;background:#fff;border:1px solid #dbeafe;border-radius:14px;padding:18px}.grid{display:grid;grid-template-columns:repeat(3,minmax(140px,1fr));gap:10px}.stat{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px}ul{list-style:none;padding:0;display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px}li{border-radius:8px;padding:10px;border:1px solid #e5e7eb;background:#fafafa}.ok{background:#ecfdf5;border-color:#10b981}.missing{background:#fef2f2;border-color:#ef4444}.count{float:right;font-weight:700;color:#334155}.badge{display:inline-block;border-radius:999px;padding:4px 8px;background:#dbeafe;color:#1d4ed8;font-weight:700}</style></head>
    <body><div class="box"><h1>Missing Keys Report</h1><p>Session: <strong><?= htmlspecialchars($sessionId) ?></strong> — total detected: <strong><?= $detectedCount ?></strong></p><div class="grid"><div class="stat"><div>Total mapped keys</div><strong><?= $total ?></strong></div><div class="stat"><div>Detected keys</div><strong><?= $detectedCount ?></strong></div><div class="stat"><div>Coverage</div><strong><?= $coverage ?>%</strong></div></div><p><span class="badge">Tip</span> Multimedia keys can be blocked by OS/browser shortcuts.</p><ul><?php foreach ($allKeys as $code): $ok = isset($detectedMap[$code]); ?><li class="<?= $ok ? 'ok' : 'missing' ?>"><?= $ok ? '✔' : '✘' ?> <?= htmlspecialchars($code) ?><?php if ($ok): ?><span class="count"><?= $counts[$code] ?></span><?php endif; ?></li><?php endforeach; ?></ul></div></body></html>
    <?php
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Keyboard & Mouse Diagnostic Dashboard</title>
    <style>
    :root {
        --bg: #05070d;
        --panel: #0d111b;
        --ink: #e2e8f0;
        --muted: #94a3b8;
        --accent: #60a5fa;
        --good: #10b981;
        --warn: #f59e0b;
    }

    * { box-sizing: border-box; }
    body {
        margin: 0; font-family: Inter, system-ui, Arial, sans-serif; color: var(--ink);
        background: radial-gradient(circle at 20% 20%, #151b2b 0%, var(--bg) 60%);
        min-height: 100vh; padding: 20px;
    }
    .container { max-width: 1250px; margin: 0 auto; }
    .card { background: rgba(15,23,42,.75); border: 1px solid rgba(148,163,184,.2); border-radius: 16px; padding: 18px; backdrop-filter: blur(8px); }
    h1 { margin: 0 0 8px; font-size: 1.5rem; }
    .lead { margin: 0 0 14px; color: var(--muted); }
    .controls { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
    .btn { border: 0; border-radius: 10px; padding: 9px 13px; font-weight: 600; cursor: pointer; background: #1f2937; color: #f8fafc; text-decoration: none; }
    .btn.primary { background: linear-gradient(120deg, #2563eb, var(--accent)); }
    .btn.good { background: linear-gradient(120deg, #059669, var(--good)); }
    .stats { display: grid; grid-template-columns: repeat(3,minmax(160px,1fr)); gap: 10px; margin-bottom: 14px; }
    .stat { background: var(--panel); border: 1px solid rgba(148,163,184,.2); border-radius: 10px; padding: 10px; }
    .stat .label { color: var(--muted); font-size: .86rem; }
    .stat .value { font-weight: 700; margin-top: 4px; }

    .keyboard-shell { background: #0b0f17; border-radius: 14px; border: 1px solid rgba(148,163,184,.18); padding: 14px; overflow-x:auto; }
    .keyboard { display: flex; flex-direction: column; gap: 8px; user-select: none; }
    .row { display: flex; gap: 8px; justify-content: center; flex-wrap: nowrap; }
    .key {
        flex: 0 0 auto;
        min-width: 42px; padding: 10px 9px; text-align: center; border-radius: 8px;
        border: 1px solid #2e3a50; color: #dbeafe;
        background: linear-gradient(180deg, #293548, #151d2c); box-shadow: inset 0 1px 0 rgba(255,255,255,.08), 0 4px 8px rgba(0,0,0,.32);
        transition: transform .07s ease, box-shadow .07s ease, background .2s ease;
        font-size: 13px;
    }
    .key.wide { min-width: 96px; }
    .key.space { min-width: 320px; }
    .key.tall { min-height: 46px; }
    .key.pressed { transform: translateY(2px); box-shadow: inset 0 3px 7px rgba(0,0,0,.4); background: linear-gradient(180deg,#1d4ed8,#1e3a8a); }
    .key.ok { border-color: rgba(16,185,129,.8); }

    .mouse-pad { margin-top: 14px; display:flex; align-items:center; justify-content:center; }
    .mouse {
        width: 170px; height: 230px; border-radius: 95px; background: linear-gradient(180deg,#d1d5db,#9ca3af);
        border:2px solid #6b7280; position:relative; box-shadow: 0 20px 32px rgba(0,0,0,.35);
    }
    .mouse-btn { position:absolute; top:12px; width:72px; height:90px; border-radius:35px 35px 10px 10px; background:#e5e7eb; border:1px solid #6b7280; }
    .mouse-btn.left { left:10px; }
    .mouse-btn.right { right:10px; }
    .wheel { position:absolute; top:44px; left:50%; transform:translateX(-50%); width:16px; height:34px; border-radius:8px; background:#4b5563; }
    .mouse-btn.active, .wheel.active { background:#60a5fa; }

    .counts { margin-top: 12px; color: var(--muted); font-size: .92rem; }
    @media (max-width: 900px) {
        .stats { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Keyboard & Mouse Diagnostic Dashboard</h1>
        <p class="lead">Test keys, function row, numpad, media keys, and mouse buttons with smoother interactions and a more realistic layout.</p>

        <div class="controls">
            <button id="resetBtn" class="btn">Reset UI</button>
            <button id="clearCountsBtn" class="btn">Clear Counts</button>
            <button id="markAllBtn" class="btn">Mark All OK</button>
            <a id="reportLink" class="btn good" href="#" target="_blank">Missing Keys Report</a>
            <button id="toggleSounds" class="btn primary">Sound: Off</button>
        </div>

        <div class="stats">
            <div class="stat"><div class="label">Last input</div><div id="lastKey" class="value">—</div></div>
            <div class="stat"><div class="label">Unique keys detected</div><div id="uniqueCount" class="value">0</div></div>
            <div class="stat"><div class="label">Session ID</div><div id="sessionBox" class="value"></div></div>
        </div>

        <div id="keyboard" class="keyboard-shell keyboard">
            <div class="row"><div class="key" data-code="Escape">Esc</div><div class="key" data-code="F1">F1</div><div class="key" data-code="F2">F2</div><div class="key" data-code="F3">F3</div><div class="key" data-code="F4">F4</div><div class="key" data-code="F5">F5</div><div class="key" data-code="F6">F6</div><div class="key" data-code="F7">F7</div><div class="key" data-code="F8">F8</div><div class="key" data-code="F9">F9</div><div class="key" data-code="F10">F10</div><div class="key" data-code="F11">F11</div><div class="key" data-code="F12">F12</div></div>
            <div class="row"><div class="key" data-code="PrintScreen">PrtSc</div><div class="key" data-code="ScrollLock">ScrLk</div><div class="key" data-code="Pause">Pause</div><div class="key" data-code="MediaTrackPrevious">⏮</div><div class="key" data-code="MediaPlayPause">⏯</div><div class="key" data-code="MediaTrackNext">⏭</div><div class="key" data-code="VolumeMute">🔇</div><div class="key" data-code="VolumeDown">🔉</div><div class="key" data-code="VolumeUp">🔊</div></div>
            <div class="row"><div class="key" data-code="Backquote">~</div><div class="key" data-code="Digit1">1</div><div class="key" data-code="Digit2">2</div><div class="key" data-code="Digit3">3</div><div class="key" data-code="Digit4">4</div><div class="key" data-code="Digit5">5</div><div class="key" data-code="Digit6">6</div><div class="key" data-code="Digit7">7</div><div class="key" data-code="Digit8">8</div><div class="key" data-code="Digit9">9</div><div class="key" data-code="Digit0">0</div><div class="key" data-code="Minus">-</div><div class="key" data-code="Equal">=</div><div class="key wide" data-code="Backspace">Backspace</div><div class="key" data-code="Insert">Ins</div><div class="key" data-code="Home">Home</div><div class="key" data-code="PageUp">PgUp</div><div class="key" data-code="NumLock">Num</div><div class="key" data-code="NumpadDivide">/</div><div class="key" data-code="NumpadMultiply">*</div><div class="key" data-code="NumpadSubtract">-</div></div>
            <div class="row"><div class="key wide" data-code="Tab">Tab</div><div class="key" data-code="KeyQ">Q</div><div class="key" data-code="KeyW">W</div><div class="key" data-code="KeyE">E</div><div class="key" data-code="KeyR">R</div><div class="key" data-code="KeyT">T</div><div class="key" data-code="KeyY">Y</div><div class="key" data-code="KeyU">U</div><div class="key" data-code="KeyI">I</div><div class="key" data-code="KeyO">O</div><div class="key" data-code="KeyP">P</div><div class="key" data-code="BracketLeft">[</div><div class="key" data-code="BracketRight">]</div><div class="key" data-code="Backslash">\</div><div class="key" data-code="Delete">Del</div><div class="key" data-code="End">End</div><div class="key" data-code="PageDown">PgDn</div><div class="key" data-code="Numpad7">7</div><div class="key" data-code="Numpad8">8</div><div class="key" data-code="Numpad9">9</div><div class="key" data-code="NumpadAdd">+</div></div>
            <div class="row"><div class="key wide" data-code="CapsLock">Caps</div><div class="key" data-code="KeyA">A</div><div class="key" data-code="KeyS">S</div><div class="key" data-code="KeyD">D</div><div class="key" data-code="KeyF">F</div><div class="key" data-code="KeyG">G</div><div class="key" data-code="KeyH">H</div><div class="key" data-code="KeyJ">J</div><div class="key" data-code="KeyK">K</div><div class="key" data-code="KeyL">L</div><div class="key" data-code="Semicolon">;</div><div class="key" data-code="Quote">'</div><div class="key wide" data-code="Enter">Enter</div><div class="key" data-code="Numpad4">4</div><div class="key" data-code="Numpad5">5</div><div class="key" data-code="Numpad6">6</div></div>
            <div class="row"><div class="key wide" data-code="ShiftLeft">Shift</div><div class="key" data-code="KeyZ">Z</div><div class="key" data-code="KeyX">X</div><div class="key" data-code="KeyC">C</div><div class="key" data-code="KeyV">V</div><div class="key" data-code="KeyB">B</div><div class="key" data-code="KeyN">N</div><div class="key" data-code="KeyM">M</div><div class="key" data-code="Comma">,</div><div class="key" data-code="Period">.</div><div class="key" data-code="Slash">/</div><div class="key wide" data-code="ShiftRight">Shift</div><div class="key" data-code="ArrowUp">↑</div><div class="key" data-code="Numpad1">1</div><div class="key" data-code="Numpad2">2</div><div class="key" data-code="Numpad3">3</div><div class="key tall" data-code="NumpadEnter">Enter</div></div>
            <div class="row"><div class="key" data-code="ControlLeft">Ctrl</div><div class="key" data-code="MetaLeft">Win</div><div class="key" data-code="AltLeft">Alt</div><div class="key space" data-code="Space">Space</div><div class="key" data-code="AltRight">Alt</div><div class="key" data-code="MetaRight">Win</div><div class="key" data-code="ContextMenu">Menu</div><div class="key" data-code="ControlRight">Ctrl</div><div class="key" data-code="ArrowLeft">←</div><div class="key" data-code="ArrowDown">↓</div><div class="key" data-code="ArrowRight">→</div><div class="key wide" data-code="Numpad0">0</div><div class="key" data-code="NumpadDecimal">.</div><div class="key" data-code="LaunchMail">Mail</div><div class="key" data-code="LaunchCalculator">Calc</div></div>
        </div>

        <div class="mouse-pad">
            <div class="mouse" aria-label="mouse visualizer">
                <div class="mouse-btn left" id="mouseLeft"></div>
                <div class="mouse-btn right" id="mouseRight"></div>
                <div class="wheel" id="mouseMiddle"></div>
            </div>
        </div>

        <div class="counts" id="counts">No keys pressed yet.</div>
    </div>
</div>
<script>
(() => {
    const sessionId = Date.now().toString(36) + Math.random().toString(36).slice(2, 9);
    const reportLink = document.getElementById('reportLink');
    const sessionBox = document.getElementById('sessionBox');
    reportLink.href = `index.php?mode=report&session_id=${encodeURIComponent(sessionId)}`;
    sessionBox.textContent = sessionId;

    const els = {
        last: document.getElementById('lastKey'), unique: document.getElementById('uniqueCount'), counts: document.getElementById('counts'), keyboard: document.getElementById('keyboard'),
        reset: document.getElementById('resetBtn'), clear: document.getElementById('clearCountsBtn'), markAll: document.getElementById('markAllBtn'), toggleSounds: document.getElementById('toggleSounds')
    };
    const mouseEls = { MouseLeft: document.getElementById('mouseLeft'), MouseMiddle: document.getElementById('mouseMiddle'), MouseRight: document.getElementById('mouseRight') };

    const keyEls = {};
    els.keyboard.querySelectorAll('[data-code]').forEach(el => { keyEls[el.dataset.code] = el; el.addEventListener('mousedown', () => hit(el.dataset.code, el.textContent.trim())); });

    const counts = {}; let queue = []; let timer = null; let sounds = false;
    function tone() {
        if (!sounds || !window.AudioContext) return;
        const ctx = new AudioContext(); const o = ctx.createOscillator(); const g = ctx.createGain();
        o.type = 'triangle'; o.frequency.value = 220; g.gain.value = 0.02; o.connect(g); g.connect(ctx.destination); o.start(); o.stop(ctx.currentTime + 0.03);
    }
    function queueLog(item) { queue.push(item); if (!timer) timer = setTimeout(flush, 120); }
    function flush() { const items = queue.splice(0); timer = null; fetch('index.php?mode=log', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({items}) }).catch(() => {}); }
    function updateStats() { const keys = Object.keys(counts); els.unique.textContent = keys.length; els.counts.textContent = keys.length ? keys.map(k => `${k}: ${counts[k]}`).join(' • ') : 'No keys pressed yet.'; }

    function hit(code, keyValue) {
        els.last.textContent = `${keyValue} · ${code}`; counts[code] = (counts[code] || 0) + 1;
        if (keyEls[code]) { keyEls[code].classList.add('pressed','ok'); setTimeout(() => keyEls[code]?.classList.remove('pressed'), 110); }
        if (mouseEls[code]) { mouseEls[code].classList.add('active'); setTimeout(() => mouseEls[code]?.classList.remove('active'), 110); }
        tone(); updateStats(); queueLog({ session_id: sessionId, key_code: code, key_value: String(keyValue || code) });
    }

    window.addEventListener('keydown', e => { if (!['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) e.preventDefault(); hit(e.code, e.key); }, { passive:false });
    window.addEventListener('mousedown', e => {
        if (e.target.closest('.key')) return;
        const map = {0:'MouseLeft',1:'MouseMiddle',2:'MouseRight'}; const code = map[e.button]; if (code) hit(code, code);
    });
    window.addEventListener('contextmenu', e => e.preventDefault());

    els.reset.addEventListener('click', () => { Object.values(keyEls).forEach(k => k.classList.remove('pressed','ok')); Object.keys(counts).forEach(k => delete counts[k]); els.last.textContent = '—'; updateStats(); });
    els.clear.addEventListener('click', () => { Object.keys(counts).forEach(k => delete counts[k]); updateStats(); });
    els.markAll.addEventListener('click', () => Object.values(keyEls).forEach(k => k.classList.add('ok')));
    els.toggleSounds.addEventListener('click', () => { sounds = !sounds; els.toggleSounds.textContent = `Sound: ${sounds ? 'On' : 'Off'}`; });
    updateStats();
})();
</script>
</body>
</html>
