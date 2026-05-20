<?php ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Microphone & Speaker Diagnostics</title>
<style>
body { margin:0; font-family:Arial,sans-serif; background:#020617; color:#e2e8f0; }
.wrap { max-width:1000px; margin:0 auto; padding:22px 14px; }
.panel { background:#0f172a; border:1px solid #334155; border-radius:12px; padding:14px; margin-bottom:14px; }
.controls { display:flex; flex-wrap:wrap; gap:8px; margin:10px 0; }
button { border:0; border-radius:8px; padding:9px 12px; font-weight:700; cursor:pointer; background:#1d4ed8; color:#fff; }
button.secondary { background:#334155; }
canvas { width:100%; height:180px; background:#020617; border:1px solid #334155; border-radius:8px; }
.stat { font-size:.95rem; color:#cbd5e1; }
.badge { font-weight:700; padding:2px 8px; border-radius:999px; }
.badge.low { background:#065f46; }
.badge.medium { background:#92400e; }
.badge.high { background:#991b1b; }
</style>
</head>
<body>
<div class="wrap">
    <h1>Microphone & Speaker Diagnostics</h1>
    <div class="panel">
        <h2>Voice Input Waveform + Noise Level Detector</h2>
        <p class="stat">Mic status: <span id="micStatus">Idle</span> | Noise: <span id="noiseValue">0</span> dB <span id="noiseBadge" class="badge low">LOW</span></p>
        <div class="controls">
            <button id="startMic">Start Mic</button>
            <button id="stopMic" class="secondary">Stop Mic</button>
        </div>
        <canvas id="wave" width="960" height="180"></canvas>
    </div>

    <div class="panel">
        <h2>Echo Test</h2>
        <p class="stat">Speak into the microphone and listen for loopback echo output.</p>
        <div class="controls">
            <button id="startEcho">Start Echo Test</button>
            <button id="stopEcho" class="secondary">Stop Echo Test</button>
        </div>
    </div>

    <div class="panel">
        <h2>Left / Right Speaker Check</h2>
        <div class="controls">
            <button id="leftTone">Play Left Channel</button>
            <button id="rightTone">Play Right Channel</button>
            <button id="bothTone" class="secondary">Play Both</button>
        </div>
    </div>
</div>
<script>
(() => {
let audioCtx, analyser, micStream, micSource, rafId, loopbackGain;
const wave = document.getElementById('wave');
const wctx = wave.getContext('2d');
const micStatus = document.getElementById('micStatus');
const noiseValue = document.getElementById('noiseValue');
const noiseBadge = document.getElementById('noiseBadge');

function ensureCtx(){ if(!audioCtx) audioCtx = new (window.AudioContext||window.webkitAudioContext)(); return audioCtx; }

async function startMic(draw=true){
  const ctx = ensureCtx();
  if (!micStream) micStream = await navigator.mediaDevices.getUserMedia({audio:true});
  if (!micSource) {
    micSource = ctx.createMediaStreamSource(micStream);
    analyser = ctx.createAnalyser();
    analyser.fftSize = 2048;
    micSource.connect(analyser);
  }
  micStatus.textContent = 'Running';
  if (draw) drawWave();
}

function stopMic(){
  if (rafId) cancelAnimationFrame(rafId);
  rafId = null;
  micStatus.textContent = 'Stopped';
}

function drawWave(){
  const buf = new Uint8Array(analyser.fftSize);
  const loop = () => {
    analyser.getByteTimeDomainData(buf);
    wctx.fillStyle='#020617'; wctx.fillRect(0,0,wave.width,wave.height);
    wctx.strokeStyle='#22d3ee'; wctx.lineWidth=2; wctx.beginPath();
    let sumSq = 0;
    for(let i=0;i<buf.length;i++){
      const v = (buf[i]-128)/128;
      sumSq += v*v;
      const x = i / (buf.length-1) * wave.width;
      const y = (1-v)*0.5 * wave.height;
      i===0 ? wctx.moveTo(x,y) : wctx.lineTo(x,y);
    }
    wctx.stroke();
    const rms = Math.sqrt(sumSq / buf.length);
    const db = rms > 0 ? 20*Math.log10(rms) : -100;
    updateNoise(db);
    rafId = requestAnimationFrame(loop);
  };
  loop();
}

function updateNoise(db){
  const rounded = Math.max(-100, Math.min(0, db)).toFixed(1);
  noiseValue.textContent = rounded;
  noiseBadge.className = 'badge';
  if (db < -45) { noiseBadge.textContent = 'LOW'; noiseBadge.classList.add('low'); }
  else if (db < -25) { noiseBadge.textContent = 'MEDIUM'; noiseBadge.classList.add('medium'); }
  else { noiseBadge.textContent = 'HIGH'; noiseBadge.classList.add('high'); }
}

async function startEcho(){
  await startMic(false);
  const ctx = ensureCtx();
  if (!loopbackGain) {
    loopbackGain = ctx.createGain();
    loopbackGain.gain.value = 0.65;
    micSource.connect(loopbackGain);
    loopbackGain.connect(ctx.destination);
  }
}
function stopEcho(){ if (loopbackGain) loopbackGain.disconnect(); loopbackGain=null; }

function playChannelTone(channel='both'){
  const ctx = ensureCtx();
  const osc = ctx.createOscillator();
  const gain = ctx.createGain();
  const splitter = ctx.createChannelSplitter(2);
  const merger = ctx.createChannelMerger(2);
  osc.frequency.value = 440; gain.gain.value = 0.15; osc.type='sine';
  osc.connect(gain); gain.connect(splitter);
  const silent = ctx.createGain(); silent.gain.value = 0;
  if (channel==='left') { splitter.connect(merger,0,0); silent.connect(merger,0,1); }
  else if (channel==='right') { silent.connect(merger,0,0); splitter.connect(merger,0,1); }
  else { splitter.connect(merger,0,0); splitter.connect(merger,0,1); }
  merger.connect(ctx.destination);
  osc.start(); osc.stop(ctx.currentTime + 1.1);
}

document.getElementById('startMic').addEventListener('click', () => startMic(true).catch(() => micStatus.textContent = 'Mic denied'));
document.getElementById('stopMic').addEventListener('click', stopMic);
document.getElementById('startEcho').addEventListener('click', () => startEcho().catch(() => micStatus.textContent = 'Mic denied'));
document.getElementById('stopEcho').addEventListener('click', stopEcho);
document.getElementById('leftTone').addEventListener('click', () => playChannelTone('left'));
document.getElementById('rightTone').addEventListener('click', () => playChannelTone('right'));
document.getElementById('bothTone').addEventListener('click', () => playChannelTone('both'));
})();
</script>
</body>
</html>
