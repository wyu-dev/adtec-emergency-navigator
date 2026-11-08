<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Sistem Navigasi Pengungsian - ADTEC Kuantan</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ff2d2d">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red: #ff2d2d;
            --orange: #ff6a00;
            --yellow: #ffd600;
            --green: #00e676;
            --dark: #070a0f;
            --dark2: #0f172a;
            --dark3: #162032;
            --text: #f8fafc;
            --muted: #94a3b8;
        }

        body {
            background: var(--dark);
            color: var(--text);
            font-family: 'Rajdhani', sans-serif;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,45,45,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,45,45,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }

        header {
            width: 100%;
            padding: 14px 18px 10px;
            text-align: center;
            border-bottom: 1px solid #1e293b;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            position: relative;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .logo-row { display: flex; align-items: center; gap: 8px; }
        .fire-icon { font-size: 1.3rem; animation: flicker 1.2s infinite alternate; }
        @keyframes flicker {
            0% { opacity: 1; transform: scale(1); }
            100% { opacity: 0.7; transform: scale(1.1); }
        }

        h1 { font-size: 1.05rem; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--red); }
        .subtitle { font-family: 'Share Tech Mono', monospace; font-size: 0.62rem; color: var(--muted); letter-spacing: 1.5px; }

        .admin-link {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.65rem;
            color: #94a3b8;
            background: #1e293b;
            border: 1px solid #334155;
            padding: 4px 10px;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .admin-link:hover { color: #f8fafc; border-color: var(--red); }

        /* Emergency Flash Banner */
        #emergency-banner {
            width: 100%;
            background: linear-gradient(90deg, #991b1b, #dc2626, #991b1b);
            color: #fff;
            padding: 10px 16px;
            text-align: center;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 1.5px;
            z-index: 15;
            box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4);
            display: none;
            animation: pulse-banner 1.5s infinite;
        }
        #emergency-banner.show { display: block; }
        @keyframes pulse-banner {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.85; }
        }

        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            padding: 16px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
        }

        #status-bar {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.72rem;
            color: var(--muted);
            letter-spacing: 1px;
            text-align: center;
            min-height: 1.2em;
            transition: color 0.3s;
        }
        #status-bar.ok { color: var(--green); }
        #status-bar.warn { color: var(--orange); }
        #status-bar.err { color: var(--red); font-weight: bold; }

        /* SOP BOX */
        #sop-box {
            display: none;
            width: 100%;
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 10px;
            padding: 12px 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }
        #sop-box.show { display: block; }
        .sop-title { font-size: 0.85rem; font-weight: 700; color: #f87171; letter-spacing: 1px; margin-bottom: 6px; display: flex; items-center; gap: 6px; }
        .sop-list { padding-left: 20px; font-size: 0.75rem; color: #cbd5e1; line-height: 1.4; font-family: 'Share Tech Mono', monospace; }

        /* Compass Visual */
        .compass-wrap { position: relative; width: 250px; height: 250px; margin: 4px 0; }

        .ring-outer {
            position: absolute; inset: 0;
            border-radius: 50%;
            border: 2px solid #223049;
            background: radial-gradient(circle at 30% 30%, #1e293b, #0a0e17);
            box-shadow: 0 0 0 1px #334155, inset 0 0 40px rgba(0,0,0,0.85), 0 4px 30px rgba(255,45,45,0.15);
        }

        .ticks { position: absolute; inset: 12px; border-radius: 50%; }
        .tick { position: absolute; width: 100%; height: 100%; top: 0; left: 0; }
        .tick::before { content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 1px; height: 8px; background: #334155; }
        .tick.major::before { height: 14px; width: 2px; background: #64748b; }
        .tick:nth-child(1)  { transform: rotate(0deg); }
        .tick:nth-child(2)  { transform: rotate(30deg); }
        .tick:nth-child(3)  { transform: rotate(60deg); }
        .tick:nth-child(4)  { transform: rotate(90deg); }
        .tick:nth-child(5)  { transform: rotate(120deg); }
        .tick:nth-child(6)  { transform: rotate(150deg); }
        .tick:nth-child(7)  { transform: rotate(180deg); }
        .tick:nth-child(8)  { transform: rotate(210deg); }
        .tick:nth-child(9)  { transform: rotate(240deg); }
        .tick:nth-child(10) { transform: rotate(270deg); }
        .tick:nth-child(11) { transform: rotate(300deg); }
        .tick:nth-child(12) { transform: rotate(330deg); }

        #arrow-container {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            transition: transform 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .arrow-svg { width: 120px; height: 120px; filter: drop-shadow(0 0 14px rgba(255,45,45,0.8)); }

        .pulse-ring { position: absolute; inset: 26px; border-radius: 50%; border: 1px solid rgba(255,45,45,0.2); animation: pulse 2s ease-out infinite; }
        .pulse-ring:nth-child(2) { animation-delay: 0.6s; }
        .pulse-ring:nth-child(3) { animation-delay: 1.2s; }
        @keyframes pulse { 0% { transform: scale(0.85); opacity: 0.6; } 100% { transform: scale(1.3); opacity: 0; } }

        .center-dot { position: absolute; width: 10px; height: 10px; border-radius: 50%; background: var(--red); box-shadow: 0 0 10px var(--red); z-index: 3; }

        #compass-source {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.6rem; letter-spacing: 1px;
            padding: 3px 10px; border-radius: 4px; display: none;
        }
        #compass-source.show   { display: inline-block; }
        #compass-source.sensor { background: rgba(0,230,118,0.15); color: var(--green);  border: 1px solid rgba(0,230,118,0.3); }
        #compass-source.orient { background: rgba(255,214,0,0.15);  color: var(--yellow); border: 1px solid rgba(255,214,0,0.3); }
        #compass-source.gps    { background: rgba(255,106,0,0.15);  color: var(--orange); border: 1px solid rgba(255,106,0,0.3); }

        .info-row { display: flex; gap: 10px; width: 100%; }
        .info-card { flex: 1; background: var(--dark3); border: 1px solid #1e293b; border-radius: 8px; padding: 10px 12px; text-align: center; }
        .info-label { font-family: 'Share Tech Mono', monospace; font-size: 0.58rem; color: var(--muted); letter-spacing: 2px; margin-bottom: 2px; }
        .info-value { font-size: 1.5rem; font-weight: 700; color: var(--text); line-height: 1; }
        .info-value.highlight { color: var(--yellow); }
        .info-value span { font-size: 0.75rem; color: var(--muted); font-weight: 500; }

        .dest-card {
            width: 100%;
            background: linear-gradient(135deg, #1e1111, #120909);
            border: 1px solid rgba(255,45,45,0.4);
            border-radius: 10px; padding: 12px 14px;
            display: flex; align-items: center; gap: 12px;
        }
        .dest-icon { font-size: 1.5rem; }
        .dest-info { flex: 1; }
        .dest-name { font-size: 0.95rem; font-weight: 700; color: #fff; }
        .dest-coords { font-family: 'Share Tech Mono', monospace; font-size: 0.6rem; color: var(--muted); margin-top: 2px; }

        .arrived-badge { display: none; background: var(--green); color: #000; font-size: 0.62rem; font-weight: 700; letter-spacing: 1px; padding: 3px 8px; border-radius: 4px; }
        .arrived-badge.show { display: block; }

        #btn-wrap { width: 100%; display: flex; flex-direction: column; gap: 8px; }

        .btn {
            padding: 12px 24px; border: none; border-radius: 8px;
            font-family: 'Rajdhani', sans-serif; font-size: 0.92rem; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase; cursor: pointer; width: 100%;
            transition: opacity 0.2s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn:active { transform: scale(0.98); }
        .btn-red    { background: var(--red); color: #fff; box-shadow: 0 4px 15px rgba(255,45,45,0.3); }
        .btn-orange { background: var(--orange); color: #fff; }
        .btn-dim    { background: #1e293b; color: var(--muted); border: 1px solid #334155; }
        .btn-emerald{ background: #059669; color: #fff; }

        /* Siren Mute Toggle */
        #siren-controls {
            display: none;
            width: 100%;
            justify-content: space-between;
            align-items: center;
            background: rgba(153, 27, 27, 0.4);
            border: 1px solid rgba(239, 68, 68, 0.5);
            padding: 8px 12px;
            border-radius: 8px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.72rem;
        }
        #siren-controls.show { display: flex; }

        /* Arrived Overlay */
        #arrived-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0, 230, 118, 0.12); z-index: 50;
            backdrop-filter: blur(4px);
            align-items: center; justify-content: center; flex-direction: column; gap: 14px;
        }
        #arrived-overlay.show { display: flex; }
        .arrived-big { font-size: 3.5rem; font-weight: 700; color: var(--green); letter-spacing: 4px; text-shadow: 0 0 20px rgba(0,230,118,0.6); }
        .arrived-sub { font-family: 'Share Tech Mono', monospace; font-size: 0.85rem; color: #e2e8f0; letter-spacing: 2px; text-align: center; }

        /* Settings Modal */
        #modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.85); z-index: 60;
            align-items: center; justify-content: center; padding: 20px;
        }
        #modal-overlay.show { display: flex; }
        .modal-box { background: #162032; border: 1px solid #334155; border-radius: 12px; padding: 22px; width: 100%; max-width: 360px; }
        .modal-title { font-size: 1rem; font-weight: 700; color: var(--orange); letter-spacing: 2px; margin-bottom: 14px; }
        .modal-step { display: flex; gap: 10px; margin-bottom: 10px; align-items: flex-start; }
        .step-num { min-width: 22px; height: 22px; border-radius: 50%; background: var(--orange); color: #000; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-top: 1px; }
        .step-text { font-family: 'Share Tech Mono', monospace; font-size: 0.72rem; color: var(--text); line-height: 1.5; }
        .step-text b { color: var(--yellow); }
    </style>
</head>
<body>

<header>
    <div class="header-brand">
        <div class="logo-row">
            <span class="fire-icon">🔥</span>
            <h1>Assembly Navigator</h1>
        </div>
        <div class="subtitle">ADTEC JTM KUANTAN · EVACUATION PWA</div>
    </div>
    <a href="{{ route('login') }}" class="admin-link">🛡️ Admin</a>
</header>

<!-- Emergency Top Banner -->
<div id="emergency-banner" class="{{ $activeAlert ? 'show' : '' }}">
    ⚠️ <span id="banner-text">{{ $activeAlert ? $activeAlert->title : 'AMARAN PENGUNGSIAN AKTIF' }}</span>
</div>

<main>
    <div id="status-bar">MEMULAKAN SISTEM PENGUNGSIAN...</div>

    <!-- Siren status & control bar -->
    <div id="siren-controls">
        <span class="flex items-center gap-1.5 text-red-200">
            <span class="animate-pulse">🔊</span>
            <span id="siren-status-text">SIREN AMARAN BERBUNYI</span>
        </span>
        <button onclick="toggleSirenMute()" id="btn-siren-mute" class="text-xs bg-red-950 text-red-300 border border-red-700 px-2.5 py-1 rounded">
            Bisukan Audio
        </button>
    </div>

    <!-- Emergency SOP Box -->
    <div id="sop-box" class="{{ $activeAlert ? 'show' : '' }}">
        <div class="sop-title">
            <span>⚠️</span>
            <span>Tindakan Keselamatan Segera:</span>
        </div>
        <ul class="sop-list">
            <li>• Jangan guna lif — gunakan tangga kecemasan.</li>
            <li>• Rendahkan badan jika terdapat asap tebal.</li>
            <li>• Ikut anak panah merah di bawah ke zon berkumpul.</li>
        </ul>
    </div>

    <!-- Compass Navigator -->
    <div class="compass-wrap">
        <div class="ring-outer"></div>
        <div class="ticks">
            <div class="tick major"></div><div class="tick"></div><div class="tick major"></div>
            <div class="tick"></div><div class="tick major"></div><div class="tick"></div>
            <div class="tick major"></div><div class="tick"></div><div class="tick major"></div>
            <div class="tick"></div><div class="tick major"></div><div class="tick"></div>
        </div>
        <div class="pulse-ring"></div>
        <div class="pulse-ring"></div>
        <div class="pulse-ring"></div>
        <div id="arrow-container">
            <svg class="arrow-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <rect x="44" y="55" width="12" height="28" rx="3" fill="#cc1111" opacity="0.8"/>
                <polygon points="50,8 78,58 50,46 22,58" fill="#ff2d2d"/>
                <polygon points="50,8 78,58 50,46 22,58" fill="url(#arrowGlow)" opacity="0.6"/>
                <defs>
                    <radialGradient id="arrowGlow" cx="50%" cy="40%" r="60%">
                        <stop offset="0%" stop-color="#ff9999"/>
                        <stop offset="100%" stop-color="#ff2d2d" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>
        <div class="center-dot"></div>
    </div>

    <span id="compass-source"></span>

    <!-- Navigation metrics -->
    <div class="info-row">
        <div class="info-card">
            <div class="info-label">Jarak</div>
            <div class="info-value highlight" id="distance">--<span>m</span></div>
        </div>
        <div class="info-card">
            <div class="info-label">Bearing</div>
            <div class="info-value" id="bearing">--<span>°</span></div>
        </div>
        <div class="info-card">
            <div class="info-label">Ketepatan GPS</div>
            <div class="info-value" id="gps-acc" style="font-size:1.1rem">--</div>
        </div>
    </div>

    <!-- Active Assembly Destination -->
    <div class="dest-card">
        <div class="dest-icon">📍</div>
        <div class="dest-info">
            <div class="dest-name" id="dest-name">{{ $assemblyPoint->name ?? 'Padang ADTEC Kuantan' }}</div>
            <div class="dest-coords" id="dest-coords">{{ $assemblyPoint->latitude ?? 3.966437 }}, {{ $assemblyPoint->longitude ?? 103.373426 }}</div>
        </div>
        <div class="arrived-badge" id="arrived-badge">SAMPAI</div>
    </div>

    <!-- Dynamic Actions (Activate sensors / Push notification subscription / Floor Plan) -->
    <div id="btn-wrap">
        <button id="btn-checkin" class="btn btn-emerald" onclick="openCheckInModal()">
            <span>✓</span>
            <span>SAHKAN KESELAMATAN (CHECK-IN)</span>
        </button>

        <button id="btn-floorplan" class="btn btn-dim" onclick="openFloorPlanModal()">
            <span>🗺️</span>
            <span>Pelan Tangga Kecemasan Bangunan</span>
        </button>

        <button id="btn-push-subscribe" class="btn btn-dim" onclick="subscribeWebPush()">
            <span>🔔</span>
            <span>Daftar Notifikasi Telefon</span>
        </button>
    </div>
</main>

<!-- Arrived Overlay -->
<div id="arrived-overlay">
    <div class="arrived-big">✓ SAMPAI</div>
    <div class="arrived-sub">ANDA TELAH TIBA DI TITIK BERKUMPUL ADTEC</div>
    <button class="btn btn-emerald" style="max-width:280px; margin-top:16px;" onclick="closeArrivedOverlay(); openCheckInModal();">
        <span>✓ DAFTAR KEHADIRAN SELAMAT</span>
    </button>
</div>

<!-- Modal Check-in Keselamatan -->
<div id="modal-checkin" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-sm hidden flex items-center justify-center p-4" style="position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:60;display:none;align-items:center;justify-content:center;padding:20px;">
    <div class="modal-box" style="background:#162032;border:1px solid #334155;border-radius:12px;padding:22px;width:100%;max-width:380px;">
        <div class="modal-title" style="color:#00e676;font-size:1.1rem;display:flex;align-items:center;gap:8px;">
            <span>🛡️</span>
            <span>Daftar Status Keselamatan</span>
        </div>
        <p style="font-family:'Share Tech Mono',monospace;font-size:0.75rem;color:#94a3b8;margin-bottom:14px;">
            Sahkan bahawa anda telah selamat keluar daripada bangunan untuk rekod headcount warden keselamatan ADTEC Kuantan.
        </p>

        <form id="checkin-form" onsubmit="submitCheckIn(event)" style="display:flex;flex-direction:column;gap:12px;">
            <div>
                <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#94a3b8;font-family:'Share Tech Mono',monospace;margin-bottom:4px;">Nama Penghuni / Pelajar</label>
                <input type="text" id="checkin-name" placeholder="cth: Ahmad Bin Razak"
                       style="width:100%;background:#070a0f;border:1px solid #334155;border-radius:6px;padding:10px;color:#fff;font-family:'Share Tech Mono',monospace;font-size:0.85rem;outline:none;">
            </div>

            <div>
                <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#94a3b8;font-family:'Share Tech Mono',monospace;margin-bottom:4px;">No. Kad Pengenalan / Matrik (Pilihan)</label>
                <input type="text" id="checkin-matric" placeholder="cth: ADTEC-2024-0199"
                       style="width:100%;background:#070a0f;border:1px solid #334155;border-radius:6px;padding:10px;color:#fff;font-family:'Share Tech Mono',monospace;font-size:0.85rem;outline:none;">
            </div>

            <div>
                <label style="display:block;font-size:0.7rem;text-transform:uppercase;color:#94a3b8;font-family:'Share Tech Mono',monospace;margin-bottom:4px;">Status Keadaan Anda</label>
                <select id="checkin-status" style="width:100%;background:#070a0f;border:1px solid #334155;border-radius:6px;padding:10px;color:#fff;font-family:'Share Tech Mono',monospace;font-size:0.85rem;outline:none;">
                    <option value="SAFE">🟢 SELAMAT DI PADANG ADTEC</option>
                    <option value="INJURED">🟡 MENGALAMI KECEDERAAN RINGAN</option>
                    <option value="TRAPPED">🔴 PERLUKAN BANTUAN SEGERA</option>
                </select>
            </div>

            <div style="display:flex;gap:10px;margin-top:10px;">
                <button type="submit" id="btn-submit-checkin" class="btn btn-emerald" style="flex:1;">
                    <span>HANTAR STATUS</span>
                </button>
                <button type="button" class="btn btn-dim" style="width:auto;padding:10px 16px;" onclick="closeCheckInModal()">
                    ✕
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pelan Lantai Tangga Kecemasan (Indoor Backup) -->
<div id="modal-floorplan" style="position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:60;display:none;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#162032;border:1px solid #334155;border-radius:12px;padding:20px;width:100%;max-width:440px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;border-bottom:1px solid #334155;padding-bottom:8px;">
            <div style="font-weight:700;color:#ff6a00;font-size:0.95rem;letter-spacing:1px;">🗺️ PELAN TANGGA KECEMASAN ADTEC</div>
            <button onclick="closeFloorPlanModal()" style="background:none;border:none;color:#94a3b8;font-size:1.2rem;cursor:pointer;">✕</button>
        </div>

        <p style="font-family:'Share Tech Mono',monospace;font-size:0.7rem;color:#94a3b8;margin-bottom:12px;">
            Gunakan pelan ini sekiranya isyarat GPS satelit lemah atau hilang sewaktu berada di dalam blok bangunan bertingkat.
        </p>

        <!-- Tactical SVG Floor Diagram -->
        <div style="background:#070a0f;border:1px solid #223049;border-radius:8px;padding:12px;display:flex;justify-content:center;">
            <svg viewBox="0 0 360 260" xmlns="http://www.w3.org/2000/svg" style="width:100%;height:auto;">
                <!-- Building Outline -->
                <rect x="20" y="20" width="320" height="170" rx="8" fill="#0f172a" stroke="#334155" stroke-width="2"/>
                <text x="180" y="42" fill="#64748b" font-size="11" font-family="monospace" text-anchor="middle" font-weight="bold">BLOK AKADEMIK & BENGKEL ADTEC</text>

                <!-- Rooms -->
                <rect x="35" y="55" width="80" height="55" fill="#1e293b" stroke="#334155" stroke-width="1"/>
                <text x="75" y="86" fill="#94a3b8" font-size="9" font-family="monospace" text-anchor="middle">MAKMAL 1</text>

                <rect x="130" y="55" width="100" height="55" fill="#1e293b" stroke="#334155" stroke-width="1"/>
                <text x="180" y="86" fill="#94a3b8" font-size="9" font-family="monospace" text-anchor="middle">KORIDOR UTAMA</text>

                <rect x="245" y="55" width="80" height="55" fill="#1e293b" stroke="#334155" stroke-width="1"/>
                <text x="285" y="86" fill="#94a3b8" font-size="9" font-family="monospace" text-anchor="middle">MAKMAL 2</text>

                <!-- Fire Stairs Left -->
                <rect x="35" y="125" width="80" height="50" fill="#450a0a" stroke="#dc2626" stroke-width="1.5"/>
                <text x="75" y="148" fill="#f87171" font-size="9" font-family="monospace" text-anchor="middle" font-weight="bold">TANGGA A</text>
                <text x="75" y="162" fill="#ef4444" font-size="8" font-family="monospace" text-anchor="middle">🔥 KELUAR</text>

                <!-- Fire Stairs Right -->
                <rect x="245" y="125" width="80" height="50" fill="#450a0a" stroke="#dc2626" stroke-width="1.5"/>
                <text x="285" y="148" fill="#f87171" font-size="9" font-family="monospace" text-anchor="middle" font-weight="bold">TANGGA B</text>
                <text x="285" y="162" fill="#ef4444" font-size="8" font-family="monospace" text-anchor="middle">🔥 KELUAR</text>

                <!-- Main Exit -->
                <rect x="145" y="175" width="70" height="15" fill="#065f46" stroke="#10b981" stroke-width="1"/>
                <text x="180" y="186" fill="#34d399" font-size="8" font-family="monospace" text-anchor="middle" font-weight="bold">PINTU KELUAR</text>

                <!-- Green Arrow to Field -->
                <line x1="180" y1="195" x2="180" y2="225" stroke="#00e676" stroke-width="3" stroke-dasharray="4,2"/>
                <polygon points="180,235 174,222 186,222" fill="#00e676"/>
                <text x="180" y="252" fill="#00e676" font-size="10" font-family="monospace" text-anchor="middle" font-weight="bold">KE PADANG ADTEC KUANTAN ➔</text>
            </svg>
        </div>

        <div style="margin-top:12px;font-family:'Share Tech Mono',monospace;font-size:0.7rem;color:#cbd5e1;line-height:1.5;">
            <p style="color:#f87171;font-weight:bold;">SOP Kecemasan Dalam Bangunan:</p>
            <p>1. Ambil tangga kecemasan terdekat (Tangga A di sayap kiri atau Tangga B di sayap kanan).</p>
            <p>2. Tolak pintu keluar kecemasan dan terus bergerak ke arah Padang ADTEC.</p>
        </div>

        <button class="btn btn-dim" style="margin-top:14px;" onclick="closeFloorPlanModal()">TUTUP PELAN</button>
    </div>
</div>

<!-- Modal Panduan Sensor -->
<div id="modal-overlay">
    <div class="modal-box">
        <div class="modal-title">⚙ BENARKAN SENSOR GERAK</div>
        <div class="modal-step">
            <div class="step-num">1</div>
            <div class="step-text">Di pelayar Chrome, ketik ikon <b>🔒</b> atau <b>ⓘ</b> di address bar.</div>
        </div>
        <div class="modal-step">
            <div class="step-num">2</div>
            <div class="step-text">Pilih <b>Site settings</b> (Tetapan laman web).</div>
        </div>
        <div class="modal-step">
            <div class="step-num">3</div>
            <div class="step-text">Cari <b>Motion sensors</b> → ubah kepada <b>Allow</b>.</div>
        </div>
        <div class="modal-step">
            <div class="step-num">4</div>
            <div class="step-text">Muat semula (reload) laman ini, kemudian aktifkan kompas semula.</div>
        </div>
        <button class="btn btn-dim" style="margin-top: 14px;" onclick="closeModal()">TUTUP</button>
    </div>
</div>

<script>
    // Configuration from server
    let ASSEMBLY = {
        lat: {{ (float) ($assemblyPoint->latitude ?? 3.966437) }},
        lng: {{ (float) ($assemblyPoint->longitude ?? 103.373426) }},
        name: "{{ addslashes($assemblyPoint->name ?? 'Padang ADTEC Kuantan') }}"
    };
    const VAPID_PUBLIC_KEY = "{{ $vapidPublicKey }}";
    const ARRIVED_THRESHOLD = 15; // meters

    let userLat = null, userLng = null;
    let compassHeading = null;
    let hasCompass = false;
    let arrived = false;
    let sensorStarted = false;
    let isEmergencyActive = {{ $activeAlert ? 'true' : 'false' }};
    let sirenMuted = false;

    // Elements
    const arrowEl = document.getElementById('arrow-container');
    const distEl = document.getElementById('distance');
    const bearingEl = document.getElementById('bearing');
    const gpsEl = document.getElementById('gps-acc');
    const statusEl = document.getElementById('status-bar');
    const btnWrap = document.getElementById('btn-wrap');
    const compassSrc = document.getElementById('compass-source');
    const arrivedBadge = document.getElementById('arrived-badge');
    const arrivedOverlay = document.getElementById('arrived-overlay');
    const emergencyBanner = document.getElementById('emergency-banner');
    const bannerText = document.getElementById('banner-text');
    const sopBox = document.getElementById('sop-box');
    const sirenControls = document.getElementById('siren-controls');
    const destNameEl = document.getElementById('dest-name');
    const destCoordsEl = document.getElementById('dest-coords');

    // ── WEB AUDIO SYNTHESIZER SIREN (Zero dependencies, works 100% offline!) ──
    let audioCtx = null;
    let sirenOsc = null;
    let sirenGain = null;
    let sirenTimer = null;

    function startEmergencySiren() {
        if (sirenMuted || sirenOsc) return;
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            sirenOsc = audioCtx.createOscillator();
            sirenGain = audioCtx.createGain();

            sirenOsc.type = 'sawtooth';
            sirenOsc.frequency.setValueAtTime(600, audioCtx.currentTime);

            // Modulate pitch between 600Hz and 1200Hz
            let up = true;
            sirenTimer = setInterval(() => {
                if (!audioCtx || !sirenOsc) return;
                const now = audioCtx.currentTime;
                if (up) {
                    sirenOsc.frequency.linearRampToValueAtTime(1100, now + 0.6);
                } else {
                    sirenOsc.frequency.linearRampToValueAtTime(600, now + 0.6);
                }
                up = !up;
            }, 600);

            sirenGain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            sirenOsc.connect(sirenGain);
            sirenGain.connect(audioCtx.destination);
            sirenOsc.start();
            sirenControls.classList.add('show');
        } catch (e) {
            console.warn("Audio Context init pending user interaction:", e);
        }
    }

    function stopEmergencySiren() {
        if (sirenTimer) { clearInterval(sirenTimer); sirenTimer = null; }
        if (sirenOsc) {
            try { sirenOsc.stop(); } catch(e) {}
            sirenOsc = null;
        }
        sirenControls.classList.remove('show');
    }

    function toggleSirenMute() {
        sirenMuted = !sirenMuted;
        const btn = document.getElementById('btn-siren-mute');
        if (sirenMuted) {
            stopEmergencySiren();
            btn.textContent = 'Bunyikan Semula';
        } else {
            if (isEmergencyActive) startEmergencySiren();
            btn.textContent = 'Bisukan Audio';
        }
    }

    // ── MATH FORMULAS ─────────────────────────────────────────
    const toRad = d => d * Math.PI / 180;

    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const a = Math.sin(toRad(lat2 - lat1) / 2) ** 2 +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(toRad(lng2 - lng1) / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function calcBearing(lat1, lng1, lat2, lng2) {
        const dLng = toRad(lng2 - lng1);
        const y = Math.sin(dLng) * Math.cos(toRad(lat2));
        const x = Math.cos(toRad(lat1)) * Math.sin(toRad(lat2)) - Math.sin(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.cos(dLng);
        return (Math.atan2(y, x) * 180 / Math.PI + 360) % 360;
    }

    // ── UI UPDATES ────────────────────────────────────────────
    function setStatus(msg, type = '') {
        statusEl.textContent = msg;
        statusEl.className = type;
    }

    function updateUI() {
        if (userLat === null) return;
        const dist = haversine(userLat, userLng, ASSEMBLY.lat, ASSEMBLY.lng);
        const bearing = calcBearing(userLat, userLng, ASSEMBLY.lat, ASSEMBLY.lng);

        distEl.innerHTML = dist < 1000 ? `${Math.round(dist)}<span>m</span>` : `${(dist / 1000).toFixed(1)}<span>km</span>`;
        bearingEl.innerHTML = `${Math.round(bearing)}<span>°</span>`;

        const angle = hasCompass && compassHeading !== null ? bearing - compassHeading : bearing;
        arrowEl.style.transform = `rotate(${angle}deg)`;

        if (dist <= ARRIVED_THRESHOLD && !arrived) {
            arrived = true;
            arrivedBadge.classList.add('show');
            arrivedOverlay.classList.add('show');
            if (navigator.vibrate) navigator.vibrate([300, 100, 300, 100, 500]);
            setTimeout(() => arrivedOverlay.classList.remove('show'), 6000);
        }
    }

    function setCompassSource(type) {
        compassSrc.classList.add('show');
        const labels = {
            sensor: '✓ KOMPAS: Sensor API',
            orient: '✓ KOMPAS: DeviceOrientation',
            gps: '⚠ GPS BEARING SAHAJA'
        };
        compassSrc.textContent = labels[type] || type;
        compassSrc.className = `show ${type}`;
    }

    // ── GPS TRACKING ─────────────────────────────────────────
    function startGPS() {
        if (!navigator.geolocation) {
            setStatus('GPS tidak disokong pada pelayar ini', 'err');
            return;
        }
        navigator.geolocation.watchPosition(
            pos => {
                userLat = pos.coords.latitude;
                userLng = pos.coords.longitude;
                const acc = Math.round(pos.coords.accuracy);
                gpsEl.innerHTML = `±${acc}<span style="font-size:0.6rem">m</span>`;
                gpsEl.style.color = acc < 20 ? '#00e676' : acc < 50 ? '#ffd600' : '#ff6a00';
                updateUI();
            },
            err => { setStatus('GPS ralat: ' + err.message, 'err'); },
            { enableHighAccuracy: true, maximumAge: 2000, timeout: 10000 }
        );
    }

    // ── SENSOR HEADINGS ──────────────────────────────────────
    function onHeading(h, source) {
        compassHeading = h;
        if (!hasCompass) {
            hasCompass = true;
            setCompassSource(source);
            setStatus(isEmergencyActive ? '🚨 KECEMASAN AKTIF — Ikut anak panah' : 'SISTEM AKTIF — Siap Sedia', 'ok');
            renderSensorBtns(false, false);
        }
        updateUI();
    }

    async function tryAbsoluteSensor() {
        if (!('AbsoluteOrientationSensor' in window)) return false;
        try {
            const perms = await Promise.all([
                navigator.permissions.query({ name: 'accelerometer' }),
                navigator.permissions.query({ name: 'magnetometer' }),
                navigator.permissions.query({ name: 'gyroscope' })
            ]);
            if (perms.some(p => p.state === 'denied')) return false;

            const sensor = new AbsoluteOrientationSensor({ frequency: 30 });
            sensor.addEventListener('error', () => {});
            sensor.addEventListener('reading', () => {
                const q = sensor.quaternion;
                const h = Math.atan2(2 * (q[3] * q[2] + q[0] * q[1]), 1 - 2 * (q[1] * q[1] + q[2] * q[2])) * 180 / Math.PI;
                onHeading((360 - ((h + 360) % 360)) % 360, 'sensor');
            });
            sensor.start();
            return true;
        } catch (e) { return false; }
    }

    function tryDeviceOrientation() {
        let gotData = false;
        const handler = (e) => {
            let h = null;
            if (typeof e.webkitCompassHeading === 'number') h = e.webkitCompassHeading;
            else if (e.alpha !== null && e.alpha !== undefined) h = (360 - e.alpha) % 360;
            if (h !== null) { gotData = true; onHeading(h, 'orient'); }
        };

        window.addEventListener('deviceorientationabsolute', handler, true);
        window.addEventListener('deviceorientation', handler, true);

        setTimeout(() => {
            if (!gotData && !hasCompass) {
                setCompassSource('gps');
                setStatus('GPS aktif — kompas tidak tersedia', 'warn');
                renderSensorBtns(true, true);
            }
        }, 3500);
    }

    async function activateCompass() {
        if (sensorStarted) return;
        sensorStarted = true;
        setStatus('Mengaktifkan kompas...', 'warn');
        renderSensorBtns(false, false);

        if (isEmergencyActive) {
            startEmergencySiren();
        }

        // iOS 13+
        if (typeof DeviceOrientationEvent?.requestPermission === 'function') {
            try {
                const r = await DeviceOrientationEvent.requestPermission();
                if (r !== 'granted') {
                    setStatus('Kebenaran kompas ditolak', 'err');
                    renderSensorBtns(true, true);
                    return;
                }
            } catch (e) {}
        }

        const ok = await tryAbsoluteSensor();
        if (!ok) tryDeviceOrientation();
    }

    function renderSensorBtns(showActivate, showSettings) {
        const activateId = 'btn-compass-activate';
        const settingsId = 'btn-compass-settings';
        let b = document.getElementById(activateId);
        let b2 = document.getElementById(settingsId);

        if (showActivate && !b) {
            b = document.createElement('button');
            b.id = activateId;
            b.className = 'btn btn-red';
            b.innerHTML = '🧭 &nbsp;AKTIFKAN KOMPAS';
            b.onclick = activateCompass;
            btnWrap.prepend(b);
        } else if (!showActivate && b) {
            b.remove();
        }

        if (showSettings && !b2) {
            b2 = document.createElement('button');
            b2.id = settingsId;
            b2.className = 'btn btn-orange';
            b2.innerHTML = '⚙ &nbsp;PANDUAN BENARKAN SENSOR';
            b2.onclick = showModal;
            btnWrap.appendChild(b2);
        } else if (!showSettings && b2) {
            b2.remove();
        }
    }

    function showModal() { document.getElementById('modal-overlay').classList.add('show'); }
    function closeModal() { document.getElementById('modal-overlay').classList.remove('show'); }

    // ── CHECK-IN & FLOORPLAN MODAL HELPERS ──────────────────
    function openCheckInModal() {
        document.getElementById('modal-checkin').style.display = 'flex';
    }
    function closeCheckInModal() {
        document.getElementById('modal-checkin').style.display = 'none';
    }
    function closeArrivedOverlay() {
        document.getElementById('arrived-overlay').classList.remove('show');
    }
    function openFloorPlanModal() {
        document.getElementById('modal-floorplan').style.display = 'flex';
    }
    function closeFloorPlanModal() {
        document.getElementById('modal-floorplan').style.display = 'none';
    }

    async function submitCheckIn(event) {
        event.preventDefault();
        const btn = document.getElementById('btn-submit-checkin');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span>⏳ Menghantar...</span>';
        btn.disabled = true;

        const payload = {
            name: document.getElementById('checkin-name').value || 'Penghuni ADTEC',
            matric_no: document.getElementById('checkin-matric').value || null,
            status: document.getElementById('checkin-status').value,
            latitude: userLat,
            longitude: userLng,
            device_id: navigator.userAgent
        };

        try {
            const res = await fetch('/api/evacuee/check-in', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (res.ok) {
                alert(data.message || 'Pendaftaran kehadiran berjaya direkodkan!');
                closeCheckInModal();
                const mainBtn = document.getElementById('btn-checkin');
                if (mainBtn) {
                    mainBtn.innerHTML = '<span>✓</span><span>STATUS TERKINI: ' + payload.status + '</span>';
                }
            } else {
                alert('Gagal menghantar pendaftaran status.');
            }
        } catch (e) {
            console.error("Check-in error:", e);
            alert('Ralat rangkaian semasa menghantar pendaftaran.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // ── REALTIME EMERGENCY STATUS POLLING (Every 3 seconds) ────
    async function checkEmergencyStatus() {
        try {
            const res = await fetch('/api/emergency/status');
            const data = await res.json();

            // Update assembly coordinates dynamically if admin modified them
            if (data.assembly_point) {
                ASSEMBLY.lat = data.assembly_point.latitude;
                ASSEMBLY.lng = data.assembly_point.longitude;
                ASSEMBLY.name = data.assembly_point.name;
                destNameEl.textContent = data.assembly_point.name;
                destCoordsEl.textContent = `${data.assembly_point.latitude}, ${data.assembly_point.longitude}`;
            }

            if (data.is_emergency) {
                if (!isEmergencyActive) {
                    // Newly triggered emergency!
                    isEmergencyActive = true;
                    emergencyBanner.classList.add('show');
                    bannerText.textContent = data.alert.title;
                    sopBox.classList.add('show');
                    setStatus('🚨 KECEMASAN AKTIF — Sila keluar sekarang!', 'err');
                    if (navigator.vibrate) navigator.vibrate([500, 250, 500, 250, 500]);
                    startEmergencySiren();
                }
            } else {
                if (isEmergencyActive) {
                    // Emergency resolved (All clear)
                    isEmergencyActive = false;
                    emergencyBanner.classList.remove('show');
                    sopBox.classList.remove('show');
                    stopEmergencySiren();
                    setStatus('🟢 KEADAAN SELAMAT (ALL CLEAR) — Sistem Siap Sedia', 'ok');
                }
            }
            updateUI();
        } catch (e) {
            console.warn("Polling status error:", e);
        }
    }

    // ── WEB PUSH SUBSCRIPTION ─────────────────────────────────
    async function subscribeWebPush() {
        const btn = document.getElementById('btn-push-subscribe');
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Pelayar anda tidak menyokong Web Push Notification.');
            return;
        }

        try {
            btn.innerHTML = '<span>⏳</span><span>Mendaftar Notifikasi...</span>';
            const registration = await navigator.serviceWorker.ready;

            let subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                const convertedVapidKey = urlBase64ToUint8Array(VAPID_PUBLIC_KEY);
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: convertedVapidKey
                });
            }

            // Send subscription to Laravel backend
            const subData = subscription.toJSON();
            const res = await fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(subData)
            });

            if (res.ok) {
                btn.className = 'btn btn-emerald';
                btn.innerHTML = '<span>✓</span><span>Notifikasi Kecemasan Aktif</span>';
                btn.disabled = true;
            }
        } catch (err) {
            console.error('Langganan push gagal:', err);
            btn.innerHTML = '<span>🔔</span><span>Daftar Notifikasi Telefon</span>';
            alert('Gagal mendaftar notifikasi. Sila pastikan kebenaran notifikasi dibenarkan di pelayar.');
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // ── INITIALIZATION ────────────────────────────────────────
    window.addEventListener('load', () => {
        setStatus('GPS sedang mengesan kedudukan...', 'warn');
        startGPS();
        renderSensorBtns(true, false);

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(reg => {
                console.log('ServiceWorker registered:', reg.scope);
            }).catch(err => {
                console.warn('ServiceWorker registration error:', err);
            });
        }

        // Start initial status poll & interval every 3 seconds
        checkEmergencyStatus();
        setInterval(checkEmergencyStatus, 3000);

        // If emergency is active upon loading, show siren & banner
        if (isEmergencyActive) {
            emergencyBanner.classList.add('show');
            sopBox.classList.add('show');
            setStatus('🚨 KECEMASAN AKTIF — Sila keluar sekarang!', 'err');
        }

        // Auto silent check for sensors
        setTimeout(() => {
            if (hasCompass || sensorStarted) return;
            const check = (e) => {
                if ((e.alpha !== null && e.alpha !== undefined) || typeof e.webkitCompassHeading === 'number') {
                    window.removeEventListener('deviceorientation', check, true);
                    window.removeEventListener('deviceorientationabsolute', check, true);
                    if (!sensorStarted) {
                        sensorStarted = true;
                        tryDeviceOrientation();
                    }
                }
            };
            window.addEventListener('deviceorientation', check, true);
            window.addEventListener('deviceorientationabsolute', check, true);
        }, 800);
    });
</script>
</body>
</html>

