<?php
$cards = [
    [
        'title' => 'Microphone & Speaker Diagnostics',
        'description' => 'Run voice waveform capture, echo testing, speaker channel checks, and ambient noise-level detection.',
        'href' => 'microphoneandspeaker/index.php',
        'cta' => 'Open Audio Dashboard'
    ],
    [
        'title' => 'Keyboard & Mouse Diagnostics',
        'description' => 'Validate key presses, media keys, mouse clicks, and generate a missing-key report.',
        'href' => 'keyboardandmouse/index.php',
        'cta' => 'Open Input Dashboard'
    ]
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Device Diagnostic Dashboard</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #0f172a; color: #e2e8f0; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 28px 16px; }
        h1 { margin-top: 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap: 16px; }
        .card { background: #111827; border: 1px solid #334155; border-radius: 12px; padding: 16px; }
        .card h2 { margin: 0 0 10px; font-size: 1.2rem; }
        .card p { color: #94a3b8; min-height: 70px; }
        .btn { display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 10px 14px; border-radius: 8px; font-weight: bold; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Device Diagnostic Dashboard</h1>
    <p>Choose a diagnostic module to test your device hardware in-browser.</p>
    <div class="grid">
        <?php foreach ($cards as $card): ?>
            <section class="card">
                <h2><?= htmlspecialchars($card['title']) ?></h2>
                <p><?= htmlspecialchars($card['description']) ?></p>
                <a class="btn" href="<?= htmlspecialchars($card['href']) ?>"><?= htmlspecialchars($card['cta']) ?></a>
            </section>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
