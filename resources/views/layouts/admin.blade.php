<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Keselamatan') - ADTEC Kuantan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: #0b0f17;
            color: #f1f5f9;
        }
        .font-mono-tech {
            font-family: 'Share Tech Mono', monospace;
        }
        .grid-bg {
            background-image: linear-gradient(rgba(239, 68, 68, 0.05) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(239, 68, 68, 0.05) 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col grid-bg antialiased selection:bg-red-600 selection:text-white">
    <!-- Top Header -->
    <header class="border-b border-slate-800 bg-slate-900/90 backdrop-blur sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl animate-pulse">🔥</span>
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-wider text-red-500 uppercase flex items-center gap-2">
                        ADTEC EVAC
                        <span class="text-xs bg-red-950 text-red-400 border border-red-800/80 px-2 py-0.5 rounded font-mono-tech">ADMIN</span>
                    </a>
                    <p class="text-xs text-slate-400 font-mono-tech hidden sm:block">SISTEM AMARAN PENGUNGSIAN & NAVIGASI KECEMASAN</p>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Status pill -->
                @php
                    $isAlertActive = \App\Models\EmergencyAlert::where('status', 'ACTIVE')->exists();
                @endphp
                @if($isAlertActive)
                    <div class="flex items-center space-x-2 bg-red-950/80 border border-red-700 text-red-300 text-xs px-3 py-1.5 rounded-full font-mono-tech font-bold animate-pulse">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <span>STATUS: KECEMASAN AKTIF</span>
                    </div>
                @else
                    <div class="flex items-center space-x-2 bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-xs px-3 py-1.5 rounded-full font-mono-tech">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>STATUS: STANDBY</span>
                    </div>
                @endif

                <a href="{{ route('evacuation.client') }}" target="_blank" class="text-xs text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 px-3 py-1.5 rounded transition font-mono-tech flex items-center gap-1.5">
                    <span>🧭</span>
                    <span class="hidden sm:inline">Paparan Pengguna</span>
                </a>

                @auth
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 bg-red-950/40 hover:bg-red-900/60 border border-red-900/50 px-3 py-1.5 rounded font-mono-tech transition">
                            Log Keluar
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('status'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-950/70 border border-emerald-700 text-emerald-200 flex items-center justify-between font-mono-tech text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-lg">✓</span>
                    <span>{{ session('status') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white text-xs">✕</button>
            </div>
        @endif

        @if(session('status_emergency'))
            <div class="mb-6 p-5 rounded-lg bg-red-950 border-2 border-red-600 text-red-100 flex items-center justify-between shadow-lg shadow-red-900/40 animate-pulse font-mono-tech">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🚨</span>
                    <div>
                        <div class="font-bold text-base tracking-wider uppercase text-red-200">{{ session('status_emergency') }}</div>
                        <div class="text-xs text-red-300">Siren dan panduan kompas telah dipancarkan kepada semua penghuni.</div>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-300 hover:text-white font-bold text-base px-2 py-1">✕</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-950/60 border border-red-800 text-red-200 font-mono-tech text-sm">
                <div class="font-bold mb-1">Ralat dikesan:</div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 bg-slate-950/90 py-4 text-center text-xs text-slate-500 font-mono-tech">
        <p>ADTEC JTM Kuantan &copy; {{ date('Y') }} · Sistem Pengungsian Bangunan Berasaskan PWA & Laravel</p>
    </footer>

    @yield('scripts')
</body>
</html>
