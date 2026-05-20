<?php
// index.php
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

    .keyboard-shell { background: #0b0f17; border-radius: 14px; border: 1px solid rgba(148,163,184,.18); padding: 14px; }
    .keyboard { display: flex; flex-direction: column; gap: 8px; user-select: none; }
    .row { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
    .key {
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
        .key.space { min-width: 180px; }
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
        </div></div>

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
    reportLink.href = `report.php?session_id=${encodeURIComponent(sessionId)}`;
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
    function flush() { const items = queue.splice(0); timer = null; fetch('log_key.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({items}) }).catch(() => {}); }
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

                    <div class="key" data-code="KeyC">C</div>
                    <div class="key" data-code="KeyV">V</div>
                    <div class="key" data-code="KeyB">B</div>
                    <div class="key" data-code="KeyN">N</div>
                    <div class="key" data-code="KeyM">M</div>
                    <div class="key" data-code="Comma">,</div>
                    <div class="key" data-code="Period">.</div>
                    <div class="key" data-code="Slash">/ ?</div>
                    <div class="key wide" data-code="ShiftRight">Shift</div>

                </div>

                <div class="row">
                    <div class="key" data-code="ControlLeft">Ctrl</div>
                    <div class="key" data-code="MetaLeft">Win</div>
                    <div class="key" data-code="AltLeft">Alt</div>
                    <div class="key space" data-code="Space">Space</div>
                    <div class="key" data-code="AltRight">Alt</div>
                    <div class="key" data-code="MetaRight">Win</div>
                    <div class="key" data-code="ContextMenu">Menu</div>
                    <div class="key" data-code="ControlRight">Ctrl</div>

                </div>
                <!-- QWERTY rows (your existing code) -->
                <div class="container-q">
                    <div>
                        <div class="row">
                            <div class="key" data-code="PrintScreen">PrintScr</div>
                            <div class="key" data-code="ScrollLock">ScrollLock</div>
                            <div class="key" data-code="Pause">Pause</div>
                        </div>
                        <div class="row">

                            <!-- Nav cluster -->
                            <div class="key" data-code="Insert">Ins</div>
                            <div class="key" data-code="Home">Home</div>
                            <div class="key" data-code="PageUp">PgUp</div>


                        </div>
                        <div class="row">


                            <!-- Nav -->
                            <div class="key" data-code="Delete">Del</div>
                            <div class="key" data-code="End">End</div>
                            <div class="key" data-code="PageDown">PgDn</div>


                        </div>

                        <div class="row">


                            <!-- Arrow keys -->
                            <div class="key" data-code="ArrowUp">↑</div>


                        </div>
                        <div class="row">


                            <!-- Arrow keys -->
                            <div class="key" data-code="ArrowLeft">←</div>
                            <div class="key" data-code="ArrowDown">↓</div>
                            <div class="key" data-code="ArrowRight">→</div>

                        </div>
                    </div>
                    <div>
                        <div class="row">
                            <!-- NumPad -->
                            <div class="key" data-code="NumLock">NumLock</div>
                            <div class="key" data-code="NumpadDivide">/</div>
                            <div class="key" data-code="NumpadMultiply">*</div>
                            <div class="key" data-code="NumpadSubtract">-</div>
                        </div>
                        <div class="row">
                            <!-- NumPad -->
                            <div class="key" data-code="Numpad7">7</div>
                            <div class="key" data-code="Numpad8">8</div>
                            <div class="key" data-code="Numpad9">9</div>
                            <div class="key" data-code="NumpadAdd">+</div>
                        </div>
                        <div class="row">
                            <!-- NumPad -->
                            <div class="key" data-code="Numpad4">4</div>
                            <div class="key" data-code="Numpad5">5</div>
                            <div class="key" data-code="Numpad6">6</div>
                        </div>

                        <div class="row">
                            <!-- NumPad -->
                            <div class="key" data-code="Numpad1">1</div>
                            <div class="key" data-code="Numpad2">2</div>
                            <div class="key" data-code="Numpad3">3</div>
                        </div>

                        <div class="row">
                            <!-- NumPad -->
                            <div class="key wide" data-code="Numpad0">0</div>
                            <div class="key" data-code="NumpadDecimal">.</div>
                            <div class="key tall" data-code="NumpadEnter">Enter</div>
                        </div>
                    </div>
                </div>


                <!-- Multimedia + Mouse Button Row -->
                <div class="row">
                    <div class="key" data-code="VolumeMute">🔇 Mute</div>
                    <div class="key" data-code="VolumeDown">🔉 Vol-</div>
                    <div class="key" data-code="VolumeUp">🔊 Vol+</div>
                    <div class="key" data-code="MediaTrackPrevious">⏮ Prev</div>
                    <div class="key" data-code="MediaPlayPause">⏯ Play/Pause</div>
                    <div class="key" data-code="MediaTrackNext">⏭ Next</div>
                    <div class="key" data-code="LaunchMail">📧 Mail</div>
                    <div class="key" data-code="LaunchApp1">🖥 App1</div>
                    <div class="key" data-code="LaunchApp2">🗂 App2</div>
                    <div class="key" data-code="LaunchCalculator">🖩 Calc</div>
                    <div class="key" data-code="BrightnessDown">☾ Bright-</div>
                    <div class="key" data-code="BrightnessUp">☼ Bright+</div>
                </div>

                <div class="row">
                    <!-- 🖱 Mouse Buttons -->
                    <div class="key" data-code="MouseLeft">🖱 Left Click</div>
                    <div class="key" data-code="MouseMiddle">🖱 Middle Click</div>
                    <div class="key" data-code="MouseRight">🖱 Right Click</div>

                </div>


                <div class="stat" id="counts" aria-live="polite">No keys pressed yet.</div>
                <div class="note">Tip: When finished testing, click <strong>Missing Keys Report</strong> to see which
                    keys your session recorded as working.</div>
            </div>

        </div>

        <script>
        (function() {
            // Unique session id
            const sessionId = Date.now().toString(36) + Math.random().toString(36).slice(2, 9);

            // Update report link
            const reportLink = document.getElementById('reportLink');
            reportLink.href = `report.php?session_id=${encodeURIComponent(sessionId)}`;

            const lastKeyEl = document.getElementById('lastKey');
            const uniqueCountEl = document.getElementById('uniqueCount');
            const countsEl = document.getElementById('counts');
            const keyboard = document.getElementById('keyboard');
            const resetBtn = document.getElementById('resetBtn');
            const clearCountsBtn = document.getElementById('clearCountsBtn');
            const markAllBtn = document.getElementById('markAllBtn');

            // map of code->element
            const keyEls = {};
            keyboard.querySelectorAll('[data-code]').forEach(el => {
                keyEls[el.dataset.code] = el;
                el.style.cursor = 'pointer';
                el.addEventListener('mousedown', () => {
                    // simulate press
                    handleKey({
                        code: el.dataset.code,
                        key: el.textContent.trim()
                    });
                    setTimeout(() => handleKeyUp({
                        code: el.dataset.code
                    }), 120);
                });
            });

            const counts = {}; // code -> count

            function updateStats() {
                const keys = Object.keys(counts);
                uniqueCountEl.textContent = keys.length;
                countsEl.textContent = keys.length ? keys.map(k => `${k}: ${counts[k]}`).join(' • ') :
                    'No keys pressed yet.';
            }

            // batching logs briefly so we don't fire too many requests
            let queue = [];
            let timer = null;

            function queueLog(payload) {
                queue.push(payload);
                if (!timer) timer = setTimeout(flushQueue, 150);
            }

            function flushQueue() {
                const items = queue.splice(0);
                timer = null;
                items.forEach(item => {
                    fetch('log_key.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(item)
                    }).catch(() => {
                        /* ignore errors silently */
                    });
                });
            }

            function handleKey(e) {
                const code = e.code || (typeof e === 'string' ? e : 'Unknown');
                const keyVal = e.key ?? code;
                lastKeyEl.textContent = `${keyVal} · ${code}`;

                // UI highlight
                const el = keyEls[code];
                if (el) {
                    el.classList.add('pressed');
                    el.classList.add('ok');
                }

                counts[code] = (counts[code] || 0) + 1;
                updateStats();

                // send log to server
                queueLog({
                    session_id: sessionId,
                    key_code: code,
                    key_value: String(keyVal)
                });
            }

            function handleKeyUp(e) {
                const code = e.code;
                const el = keyEls[code];
                if (el) el.classList.remove('pressed');
            }

            window.addEventListener('keydown', e => {
                // prevent default scrolling for space etc. only when not in an input
                if (!['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                }
                handleKey(e);
            }, {
                passive: false
            });

            window.addEventListener('keyup', e => {
                handleKeyUp(e);
            });

            resetBtn.addEventListener('click', () => {
                Object.values(keyEls).forEach(k => k.classList.remove('ok', 'pressed'));
                for (let p in counts) delete counts[p];
                lastKeyEl.textContent = '—';
                updateStats();
            });

            clearCountsBtn.addEventListener('click', () => {
                for (let p in counts) delete counts[p];
                updateStats();
            });

            markAllBtn.addEventListener('click', () => {
                Object.values(keyEls).forEach(k => k.classList.add('ok'));
            });

            updateStats();
        })();
        </script>
</body>

<footer style="background:#111; color:#eee; padding:20px 10px; text-align:center; font-size:14px;">
    <p>
        &copy; <span id="year"></span> Keyboard Tester Tool — Created by
        <strong>Anupam Manna</strong>
        <span style="color:#888;">(Data Scientist &amp; Software Developer)</span>
    </p>
    <p>
        📧 Email: <a href="mailto:contact@keyboard-tester.free.nf" style="color:#00bfff;">am7059141480@gmail.com</a> |
        | 📱 Phone: <span>+91</span><span>7059</span><span>141480</span>
    </p>
    <p>
        <a href="https://keyboard-tester.free.nf/privacy-policy" style="color:#bbb;">Privacy Policy</a> |
        <a href="https://keyboard-tester.free.nf/terms" style="color:#bbb;">Terms of Service</a>
    </p>
    <script>
    document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</footer>

</html>