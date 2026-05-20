<?php
// index.php
// Frontend is client-side JS. Place config.php & log_key.php in same folder.
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>Keyboard Tester | Online Keyboard Test with Multimedia & Missing Keys Report</title>
    <meta name="description"
        content="Test your keyboard online — including multimedia keys, function keys, numpad, arrow keys, and mouse buttons. Instantly see missing keys in the report. Created by Anupam Manna (Data Scientist & Software Developer).">
    <meta name="keywords"
        content="keyboard tester, online keyboard test, multimedia key test, missing keys report, keyboard checker, numpad tester, arrow key test, mouse button test, function keys, F1-F12 test, spacebar test, laptop keyboard test, Anupam Manna">
    <meta name="author" content="Anupam Manna">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">

    <!-- Open Graph -->
    <meta property="og:title" content="Keyboard Tester — Test All Keys & Get Missing Keys Report">
    <meta property="og:description"
        content="Free online keyboard tester to check every key including multimedia, mouse buttons, and numpad. Get a Missing Keys Report instantly. Created by Anupam Manna.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://keyboard-tester.free.nf/">
    <meta property="og:image" content="https://keyboard-tester.free.nf/assets/keyboard-tester-preview.png">
    <meta property="og:site_name" content="Keyboard Tester Tool">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Keyboard Tester — Online Multimedia & Missing Keys Report">
    <meta name="twitter:description"
        content="Check your keyboard online for all keys including multimedia, arrow, numpad, and mouse buttons. Created by Anupam Manna.">
    <meta name="twitter:image" content="https://keyboard-tester.free.nf/assets/keyboard-tester-preview.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://keyboard-tester.free.nf/">

    <!-- Favicon -->
    <link rel="icon" href="https://keyboard-tester.free.nf/favicon.ico" type="image/x-icon">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Keyboard Tester",
        "url": "https://keyboard-tester.free.nf/",
        "image": "https://keyboard-tester.free.nf/assets/keyboard-tester-preview.png",
        "description": "Free online keyboard testing tool that checks all keys including multimedia keys, function keys, numpad, mouse buttons, and generates a Missing Keys Report.",
        "applicationCategory": "UtilityApplication",
        "operatingSystem": "Any",
        "creator": {
            "@type": "Person",
            "name": "Anupam Manna",
            "jobTitle": "Data Scientist & Software Developer",
            "url": "https://keyboard-tester.free.nf/"
        }
    }
    </script>


    <style>
    :root {
        --bg: #071029;
        --muted: #94a3b8;
        --accent: #60a5fa
    }

    body {
        font-family: Inter, system-ui, Roboto, Arial;
        margin: 0;
        padding: 18px;
        background: linear-gradient(180deg, #071021, #081426);
        color: #e6eef8
    }

    .container {
        max-width: 1100px;
        margin: 0 auto
    }

    .container-q {
        display: flex;
        justify-content: center;
        width: 100%;
        padding: 16px 0;
    }

    .card {
        background: rgba(255, 255, 255, 0.02);
        padding: 16px;
        border-radius: 10px
    }

    h1 {
        margin: 0 0 6px
    }

    p.lead {
        color: var(--muted);
        margin: 0 0 12px
    }

    .controls {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 12px
    }

    button,
    a.button {
        padding: 8px 12px;
        border-radius: 8px;
        border: 0;
        background: var(--accent);
        color: #fff;
        text-decoration: none;
        cursor: pointer
    }

    .keyboard {
        display: flex;
        flex-direction: column;
        gap: 8px;
        user-select: none
    }

    .row {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap
    }

    .key {
        min-width: 36px;
        padding: 8px 10px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 8px;
        text-align: center;
        font-size: 13px;
        border: 1px solid rgba(255, 255, 255, 0.03)
    }

    .key.wide {
        min-width: 88px
    }

    .key.space {
        min-width: 320px
    }

    .key.pressed {
        background: linear-gradient(90deg, var(--accent), #2563eb);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.18);
        transform: translateY(-1px)
    }

    .key.ok {
        border: 1px solid rgba(16, 185, 129, 0.12)
    }

    .stat {
        background: rgba(255, 255, 255, 0.02);
        padding: 10px;
        border-radius: 8px;
        min-width: 160px;
        margin-top: 12px;
        color: var(--muted)
    }

    .note {
        color: var(--muted);
        margin-top: 10px;
        font-size: 13px
    }

    @media (max-width:700px) {
        .key.space {
            min-width: 180px
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Keyboard Tester</h1>
            <p class="lead">Press keys (including multimedia). Results log to the server. When finished, open the
                Missing Keys Report for this session.</p>

            <div class="controls">
                <button id="resetBtn">Reset UI</button>
                <button id="clearCountsBtn">Clear Counts</button>
                <button id="markAllBtn">Mark All OK</button>
                <a id="reportLink" class="button" href="#" target="_blank" style="background:#10b981">Missing Keys
                    Report</a>
                <!-- <a class="button" href="view_logs.php" target="_blank" style="background:#2563eb">View All Logs</a> -->
            </div>

            <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
                <div>
                    <div style="font-weight:700;font-size:18px" id="lastKey">—</div>
                    <div style="color:var(--muted)">Last key pressed (value · code)</div>
                </div>
                <div style="margin-left:auto;text-align:right">
                    <div style="color:var(--muted)">Unique keys detected</div>
                    <div id="uniqueCount" style="font-weight:700;font-size:18px">0</div>
                </div>
            </div>

            <div id="keyboard" class="keyboard">
                <!-- Row 1 -->
                <div class="row">
                    <div class="key" data-code="Escape">Esc</div>
                    <div class="key" data-code="F1">F1</div>
                    <div class="key" data-code="F2">F2</div>
                    <div class="key" data-code="F3">F3</div>
                    <div class="key" data-code="F4">F4</div>
                    <div class="key" data-code="F5">F5</div>
                    <div class="key" data-code="F6">F6</div>
                    <div class="key" data-code="F7">F7</div>
                    <div class="key" data-code="F8">F8</div>
                    <div class="key" data-code="F9">F9</div>
                    <div class="key" data-code="F10">F10</div>
                    <div class="key" data-code="F11">F11</div>
                    <div class="key" data-code="F12">F12</div>

                </div>

                <!-- QWERTY rows (your existing code) -->
                <div class="row">
                    <div class="key" data-code="Backquote">~ `</div>
                    <div class="key" data-code="Digit1">1</div>
                    <div class="key" data-code="Digit2">2</div>
                    <div class="key" data-code="Digit3">3</div>
                    <div class="key" data-code="Digit4">4</div>
                    <div class="key" data-code="Digit5">5</div>
                    <div class="key" data-code="Digit6">6</div>
                    <div class="key" data-code="Digit7">7</div>
                    <div class="key" data-code="Digit8">8</div>
                    <div class="key" data-code="Digit9">9</div>
                    <div class="key" data-code="Digit0">0</div>
                    <div class="key" data-code="Minus">- _</div>
                    <div class="key" data-code="Equal">= +</div>
                    <div class="key wide" data-code="Backspace">Backspace</div>


                </div>

                <div class="row">
                    <div class="key wide" data-code="Tab">Tab</div>
                    <div class="key" data-code="KeyQ">Q</div>
                    <div class="key" data-code="KeyW">W</div>
                    <div class="key" data-code="KeyE">E</div>
                    <div class="key" data-code="KeyR">R</div>
                    <div class="key" data-code="KeyT">T</div>
                    <div class="key" data-code="KeyY">Y</div>
                    <div class="key" data-code="KeyU">U</div>
                    <div class="key" data-code="KeyI">I</div>
                    <div class="key" data-code="KeyO">O</div>
                    <div class="key" data-code="KeyP">P</div>
                    <div class="key" data-code="BracketLeft">[ {</div>
                    <div class="key" data-code="BracketRight">] }</div>
                    <div class="key" data-code="Backslash">\ |</div>


                </div>

                <div class="row">
                    <div class="key wide" data-code="CapsLock">Caps</div>
                    <div class="key" data-code="KeyA">A</div>
                    <div class="key" data-code="KeyS">S</div>
                    <div class="key" data-code="KeyD">D</div>
                    <div class="key" data-code="KeyF">F</div>
                    <div class="key" data-code="KeyG">G</div>
                    <div class="key" data-code="KeyH">H</div>
                    <div class="key" data-code="KeyJ">J</div>
                    <div class="key" data-code="KeyK">K</div>
                    <div class="key" data-code="KeyL">L</div>
                    <div class="key" data-code="Semicolon">; :</div>
                    <div class="key" data-code="Quote">' "</div>
                    <div class="key wide" data-code="Enter">Enter</div>
                </div>

                <div class="row">
                    <div class="key wide" data-code="ShiftLeft">Shift</div>
                    <div class="key" data-code="KeyZ">Z</div>
                    <div class="key" data-code="KeyX">X</div>
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