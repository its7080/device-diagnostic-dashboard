<?php
// Ultimate Device Diagnostic Suite (single-page starter)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ultimate Device Diagnostic Suite</title>
  <style>
    :root{--bg:#0b1020;--card:#141b33;--text:#e9eefc;--muted:#9fb0d9;--accent:#4f8cff;--ok:#22c55e;--warn:#f59e0b}
    *{box-sizing:border-box} body{margin:0;font-family:Inter,Arial,sans-serif;background:linear-gradient(180deg,#0b1020,#070b17);color:var(--text)}
    .wrap{max-width:1200px;margin:0 auto;padding:20px}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:14px}
    .card{background:var(--card);border:1px solid #1f2a4e;border-radius:12px;padding:14px}.card h3{margin:0 0 10px}
    .pill{display:inline-block;background:#1f2a4e;color:var(--muted);border-radius:999px;padding:4px 8px;font-size:12px;margin:2px}
    .btn{background:var(--accent);color:#fff;border:0;border-radius:8px;padding:8px 12px;cursor:pointer}
    #mousePad{height:170px;border:1px dashed #39508a;border-radius:8px;position:relative;overflow:hidden}
    .dot{position:absolute;width:10px;height:10px;border-radius:50%;background:#f43f5e;opacity:.7;transform:translate(-50%,-50%)}
    .keys{display:grid;grid-template-columns:repeat(10,1fr);gap:6px}.key{padding:6px;text-align:center;border:1px solid #2a3869;border-radius:6px}
    .key.on{background:#1e3a8a;border-color:#60a5fa}
    canvas{width:100%;height:120px;background:#0a1124;border:1px solid #2a3869;border-radius:8px}
    video{width:100%;border-radius:8px;border:1px solid #2a3869}.muted{color:var(--muted);font-size:14px}
  </style>
</head>
<body>
<div class="wrap">
  <h1>🖥️ Ultimate Device Diagnostic Suite</h1>
  <p class="muted">Starter portfolio project in <strong>PHP + JavaScript</strong>: mouse, keyboard, mic, camera, monitor, and network diagnostics.</p>

  <div class="grid">
    <section class="card">
      <h3>1) Mouse Button + CPS Tester</h3>
      <div><span class="pill" id="lc">Left: 0</span><span class="pill" id="rc">Right: 0</span><span class="pill" id="dc">Double: 0</span><span class="pill" id="sc">Scroll: 0</span><span class="pill" id="cps">CPS: 0</span></div>
      <p class="muted">Click / right-click / double-click and drag inside pad.</p>
      <div id="mousePad"></div>
    </section>

    <section class="card">
      <h3>2) Keyboard Testing</h3>
      <div class="keys" id="keys"></div>
      <p class="muted">Ghosting/basic rollover visibility + typing speed sample.</p>
      <div class="pill" id="kCount">Unique Keys: 0</div>
    </section>

    <section class="card">
      <h3>3) Microphone + Speaker Test</h3>
      <button class="btn" id="micBtn">Start Microphone</button>
      <canvas id="micCanvas" width="500" height="120"></canvas>
      <p class="muted" id="noise">Noise: --</p>
      <button class="btn" id="beepBtn">Play Left/Right Beep</button>
    </section>

    <section class="card">
      <h3>4) Webcam Testing</h3>
      <button class="btn" id="camBtn">Start Camera</button>
      <video id="cam" autoplay playsinline muted></video>
      <p class="muted" id="camInfo">FPS / Resolution: --</p>
    </section>

    <section class="card">
      <h3>5) Monitor Dead Pixel Tester</h3>
      <button class="btn" onclick="document.body.style.background='#000'">Black</button>
      <button class="btn" onclick="document.body.style.background='#fff';document.body.style.color='#111'">White</button>
      <button class="btn" onclick="document.body.style.background='red'">Red</button>
      <button class="btn" onclick="document.body.style.background='green'">Green</button>
      <button class="btn" onclick="document.body.style.background='blue'">Blue</button>
    </section>

    <section class="card">
      <h3>6) Browser/Network Diagnostics</h3>
      <div id="env"></div>
    </section>
  </div>
</div>
<script>
const pad=document.getElementById('mousePad');let left=0,right=0,dbl=0,scroll=0,clickTimes=[];
const set=(id,v)=>document.getElementById(id).textContent=v;
pad.addEventListener('click',e=>{left++;clickTimes.push(Date.now());dot(e);});
pad.addEventListener('contextmenu',e=>{e.preventDefault();right++;dot(e);});
pad.addEventListener('dblclick',e=>{dbl++;dot(e);});
pad.addEventListener('wheel',()=>scroll++);
function dot(e){const d=document.createElement('div');d.className='dot';d.style.left=e.offsetX+'px';d.style.top=e.offsetY+'px';pad.appendChild(d);setTimeout(()=>d.remove(),1500)}
setInterval(()=>{const now=Date.now();clickTimes=clickTimes.filter(t=>now-t<1000);set('lc',`Left: ${left}`);set('rc',`Right: ${right}`);set('dc',`Double: ${dbl}`);set('sc',`Scroll: ${scroll}`);set('cps',`CPS: ${clickTimes.length}`)},100);

const codes=['KeyQ','KeyW','KeyE','KeyR','KeyT','KeyY','KeyU','KeyI','KeyO','KeyP','KeyA','KeyS','KeyD','KeyF','KeyG','KeyH','KeyJ','KeyK','KeyL','Enter','KeyZ','KeyX','KeyC','KeyV','KeyB','KeyN','KeyM','Space','ShiftLeft','ControlLeft'];
const box=document.getElementById('keys');const seen=new Set();codes.forEach(c=>{const el=document.createElement('div');el.className='key';el.id=c;el.textContent=c;box.appendChild(el)});
addEventListener('keydown',e=>{seen.add(e.code);const el=document.getElementById(e.code);if(el)el.classList.add('on');document.getElementById('kCount').textContent='Unique Keys: '+seen.size});
addEventListener('keyup',e=>{const el=document.getElementById(e.code);if(el)el.classList.remove('on')});

let analyser,dataArray,audioCtx;document.getElementById('micBtn').onclick=async()=>{try{const s=await navigator.mediaDevices.getUserMedia({audio:true});audioCtx=new AudioContext();const src=audioCtx.createMediaStreamSource(s);analyser=audioCtx.createAnalyser();analyser.fftSize=256;src.connect(analyser);dataArray=new Uint8Array(analyser.frequencyBinCount);drawMic()}catch(e){alert('Mic denied: '+e.message)}};
function drawMic(){if(!analyser)return;analyser.getByteFrequencyData(dataArray);const c=document.getElementById('micCanvas'),x=c.getContext('2d');x.clearRect(0,0,c.width,c.height);let sum=0;for(let i=0;i<dataArray.length;i++){const v=dataArray[i];sum+=v;x.fillStyle='#4f8cff';x.fillRect(i*2,c.height-v/2,1,v/2)}document.getElementById('noise').textContent='Noise: '+Math.round(sum/dataArray.length);requestAnimationFrame(drawMic)}

document.getElementById('beepBtn').onclick=()=>{const ctx=new (window.AudioContext||window.webkitAudioContext)();const osc=ctx.createOscillator();const pan=ctx.createStereoPanner();osc.connect(pan).connect(ctx.destination);osc.frequency.value=540;osc.start();pan.pan.value=-1;setTimeout(()=>pan.pan.value=1,300);setTimeout(()=>osc.stop(),600)};

document.getElementById('camBtn').onclick=async()=>{try{const stream=await navigator.mediaDevices.getUserMedia({video:true});const v=document.getElementById('cam');v.srcObject=stream;const tr=stream.getVideoTracks()[0].getSettings();document.getElementById('camInfo').textContent=`Resolution: ${tr.width}x${tr.height} | FPS: ${tr.frameRate||'n/a'}`}catch(e){alert('Camera denied: '+e.message)}};

document.getElementById('env').innerHTML=`<div class="pill">UA: ${navigator.userAgent}</div><div class="pill">Cookies: ${navigator.cookieEnabled}</div><div class="pill">Screen: ${screen.width}x${screen.height}</div><div class="pill">Language: ${navigator.language}</div><div class="pill">Online: ${navigator.onLine}</div>`;
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