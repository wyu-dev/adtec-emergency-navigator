@extends('layouts.admin')

@section('title', 'Papan Pemuka Keselamatan')

@section('content')
<div class="space-y-6">

    <!-- 1. EMERGENCY COMMAND BANNER -->
    @if($activeAlert)
        <div class="bg-gradient-to-r from-red-950 via-red-900 to-red-950 border-2 border-red-500 rounded-2xl p-6 shadow-2xl shadow-red-950/80 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl select-none pointer-events-none">🚨</div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 bg-red-600 text-white font-bold px-3 py-1 rounded-full text-xs font-mono-tech tracking-wider uppercase animate-bounce">
                        <span>🚨 {{ $activeAlert->type === 'DRILL' ? 'LATIHAN PENGUNGSIAN (DRILL)' : 'KECEMASAN SEBENAR (REAL)' }}</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-white tracking-wide uppercase">{{ $activeAlert->title }}</h2>
                    <p class="text-red-200 text-sm max-w-2xl font-mono-tech">{{ $activeAlert->message }}</p>
                    <div class="flex flex-wrap items-center gap-4 text-xs font-mono-tech text-red-300 pt-2">
                        <span>📍 Zon Berkumpul: <strong class="text-white">{{ $activeAlert->assemblyPoint?->name ?? 'Padang ADTEC Kuantan' }}</strong></span>
                        <span>⏱ Dicetuskan: <strong class="text-white">{{ $activeAlert->triggered_at->format('d/m/Y H:i:s') }}</strong></span>
                        <span>👤 Oleh: <strong class="text-white">{{ $activeAlert->triggered_by }}</strong></span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <form action="{{ route('admin.alert.resolve') }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu menamatkan amaran kecemasan ini? Notifikasi All-Clear akan dihantar.');">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-6 py-4 bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold rounded-xl uppercase tracking-wider text-sm transition shadow-lg shadow-emerald-900/50 flex items-center justify-center gap-2">
                            <span>🟢</span>
                            <span>TAMATKAN AMARAN (ALL CLEAR)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 bg-emerald-950 border border-emerald-700/80 text-emerald-300 font-bold px-3 py-1 rounded-full text-xs font-mono-tech tracking-wider uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>SISTEM DALAM KEADAAN SIAP SEDIA (STANDBY)</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white tracking-wide">Pusat Kawalan Amaran Kecemasan Bangunan</h2>
                    <p class="text-slate-400 text-xs font-mono-tech">
                        Tiada amaran kecemasan aktif pada masa ini. Bersedia untuk mencetuskan amaran jika berlaku sebarang insiden.
                    </p>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button onclick="openTriggerModal('REAL')"
                            class="flex-1 md:flex-none px-6 py-3.5 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl uppercase tracking-wider text-sm transition shadow-lg shadow-red-950/60 flex items-center justify-center gap-2">
                        <span>🔥</span>
                        <span>AMARAN KECEMASAN</span>
                    </button>
                    <button onclick="openTriggerModal('DRILL')"
                            class="flex-1 md:flex-none px-5 py-3.5 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-xl uppercase tracking-wider text-sm transition shadow-lg shadow-amber-950/60 flex items-center justify-center gap-2">
                        <span>📢</span>
                        <span>LATIHAN (DRILL)</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. QUICK STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Active Zone -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4">
            <div class="text-xs font-mono-tech text-slate-400 uppercase tracking-wider mb-1">Titik Berkumpul Semasa</div>
            <div class="text-lg font-bold text-amber-400 truncate">{{ $activeAssemblyPoint->name ?? 'Padang ADTEC' }}</div>
            <div class="text-xs font-mono-tech text-slate-500 mt-1">
                {{ number_format($activeAssemblyPoint->latitude ?? 3.966437, 6) }}, {{ number_format($activeAssemblyPoint->longitude ?? 103.373426, 6) }}
            </div>
        </div>

        <!-- Card 2: Push Subscribers -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4">
            <div class="text-xs font-mono-tech text-slate-400 uppercase tracking-wider mb-1">Peranti Pelajar Berdaftar</div>
            <div class="text-2xl font-bold text-emerald-400 font-mono-tech">{{ $subscriberCount }} <span class="text-xs text-slate-500 font-normal">peranti</span></div>
            <div class="text-xs font-mono-tech text-slate-500 mt-1">Web Push Notification Aktif</div>
        </div>

        <!-- Card 3: Emergency PIN -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 flex items-center justify-between">
            <div>
                <div class="text-xs font-mono-tech text-slate-400 uppercase tracking-wider mb-1">PIN Pantas Semasa</div>
                <div class="text-2xl font-bold text-slate-200 font-mono-tech tracking-widest">{{ $emergencyPin }}</div>
                <div class="text-xs font-mono-tech text-slate-500 mt-1">Digunakan untuk pintasan cemas</div>
            </div>
            <button onclick="openSettingsModal()" class="text-slate-400 hover:text-amber-400 p-2 text-xl" title="Tukar PIN">
                ⚙️
            </button>
        </div>

        <!-- Card 4: Quick PWA Shortcut -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-4 flex items-center justify-between">
            <div>
                <div class="text-xs font-mono-tech text-slate-400 uppercase tracking-wider mb-1">Aplikasi Navigasi PWA</div>
                <div class="text-sm font-bold text-slate-200">Panduan Kompas Telefon</div>
                <div class="text-xs font-mono-tech text-emerald-400 mt-1">✓ PWA Ready & Offline</div>
            </div>
            <a href="{{ route('evacuation.client') }}" target="_blank"
               class="p-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-lg transition border border-slate-700" title="Buka Navigasi">
                🧭
            </a>
        </div>
    </div>

    <!-- 3. ASSEMBLY POINTS SECTION -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold text-white tracking-wider uppercase flex items-center gap-2">
                    <span>📍</span>
                    <span>Pengurusan Zon Berkumpul (Assembly Points)</span>
                </h3>
                <p class="text-xs text-slate-400 font-mono-tech">
                    Koordinat sasaran kompas dan GPS yang digunakan untuk memandu pelajar sewaktu evakuasi.
                </p>
            </div>
            <button onclick="openAddPointModal()"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-amber-400 hover:text-amber-300 text-xs font-bold font-mono-tech rounded-lg uppercase tracking-wider transition flex items-center gap-1.5">
                <span>➕</span>
                <span>Tambah Zon Baharu</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono-tech">
                <thead class="bg-slate-950/60 text-slate-400 uppercase border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Nama Lokasi Zon</th>
                        <th class="py-3 px-4">Latitude</th>
                        <th class="py-3 px-4">Longitude</th>
                        <th class="py-3 px-4">Penerangan</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($assemblyPoints as $point)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4">
                                @if($point->is_active)
                                    <span class="inline-flex items-center gap-1 bg-emerald-950 text-emerald-300 border border-emerald-700 px-2.5 py-0.5 rounded-full font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>ZON AKTIF</span>
                                    </span>
                                @else
                                    <span class="text-slate-500 font-normal">SIMPANAN</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-white text-sm">
                                {{ $point->name }}
                            </td>
                            <td class="py-3 px-4 text-amber-400">{{ $point->latitude }}</td>
                            <td class="py-3 px-4 text-amber-400">{{ $point->longitude }}</td>
                            <td class="py-3 px-4 text-slate-400 max-w-xs truncate">{{ $point->description ?? '-' }}</td>
                            <td class="py-3 px-4 text-right space-x-2">
                                @if(!$point->is_active)
                                    <form action="{{ route('admin.assembly.active', $point) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-950/60 hover:bg-emerald-900 text-emerald-300 border border-emerald-800/60 rounded text-xs transition">
                                            Jadikan Aktif
                                        </button>
                                    </form>
                                @endif
                                <button onclick="openEditPointModal({{ json_encode($point) }})"
                                        class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 rounded text-xs transition">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-500">Tiada zon berkumpul didaftarkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. RECENT AUDIT LOGS -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800">
            <h3 class="text-lg font-bold text-white tracking-wider uppercase flex items-center gap-2">
                <span>📋</span>
                <span>Log & Sejarah Amaran Keselamatan</span>
            </h3>
            <p class="text-xs text-slate-400 font-mono-tech">
                Rekod audit semua insiden amaran kecemasan dan latihan kebakaran yang pernah dicetuskan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-mono-tech">
                <thead class="bg-slate-950/60 text-slate-400 uppercase border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Tajuk Amaran</th>
                        <th class="py-3 px-4">Dicetuskan Oleh</th>
                        <th class="py-3 px-4">Masa Mula</th>
                        <th class="py-3 px-4">Masa Selesai</th>
                        <th class="py-3 px-4">Zon Sasaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($recentAlerts as $alert)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4">
                                @if($alert->status === 'ACTIVE')
                                    <span class="bg-red-950 text-red-300 border border-red-700 px-2 py-0.5 rounded-full font-bold">AKTIF</span>
                                @elseif($alert->status === 'ALL_CLEAR')
                                    <span class="bg-emerald-950 text-emerald-300 border border-emerald-800 px-2 py-0.5 rounded-full">SELESAI</span>
                                @else
                                    <span class="text-slate-500">{{ $alert->status }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="{{ $alert->type === 'REAL' ? 'text-red-400 font-bold' : 'text-amber-400' }}">
                                    {{ $alert->type }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-white">{{ $alert->title }}</td>
                            <td class="py-3 px-4 text-slate-400">{{ $alert->triggered_by }}</td>
                            <td class="py-3 px-4 text-slate-400">{{ $alert->triggered_at ? $alert->triggered_at->format('d/m/Y H:i:s') : '-' }}</td>
                            <td class="py-3 px-4 text-slate-400">{{ $alert->resolved_at ? $alert->resolved_at->format('d/m/Y H:i:s') : ($alert->status === 'ACTIVE' ? 'SEDANG BERJALAN' : '-') }}</td>
                            <td class="py-3 px-4 text-slate-300">{{ $alert->assemblyPoint?->name ?? 'Padang ADTEC' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">Tiada rekod amaran ditemui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: TRIGGER EMERGENCY ALERT -->
<div id="modal-trigger" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border-2 border-red-600 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🚨</span>
                <h3 class="text-xl font-bold uppercase text-white tracking-wider" id="trigger-modal-title">Cetuskan Amaran Kecemasan</h3>
            </div>
            <button onclick="closeModal('modal-trigger')" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.alert.trigger') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Jenis Amaran</label>
                <select name="type" id="alert-type-select" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-red-500 focus:outline-none">
                    <option value="REAL">🚨 KECEMASAN SEBENAR (REAL EMERGENCY)</option>
                    <option value="DRILL">📢 LATIHAN KECEMASAN (FIRE DRILL)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Tahap Kritikal (Severity)</label>
                <select name="severity" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-red-500 focus:outline-none">
                    <option value="CRITICAL">🔴 KRITIKAL (CRITICAL - Pengungsian Penuh)</option>
                    <option value="WARNING">🟡 AMARAN (WARNING - Siap Sedia)</option>
                    <option value="INFO">🔵 MAKLUMAT (INFO)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Titik Zon Berkumpul Sasaran</label>
                <select name="assembly_point_id" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-red-500 focus:outline-none">
                    @foreach($assemblyPoints as $p)
                        <option value="{{ $p->id }}" {{ $p->is_active ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->latitude }}, {{ $p->longitude }}) {{ $p->is_active ? '★ AKTIF' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Tajuk Notifikasi</label>
                <input type="text" name="title" id="alert-title-input" value="AMARAN PENGUNGSIAN BANGUNAN!" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-red-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Mesej Arahan Keselamatan</label>
                <textarea name="message" id="alert-message-input" rows="3" required
                          class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white font-mono-tech text-sm focus:border-red-500 focus:outline-none">Kecemasan dikesan! Sila keluar ke Padang ADTEC Kuantan dengan tenang dan ikut anak panah panduan pada skrin anda.</textarea>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full py-4 bg-red-600 hover:bg-red-500 text-white font-bold rounded-xl uppercase tracking-wider text-sm transition shadow-lg shadow-red-950 flex items-center justify-center gap-2">
                    <span>🚨</span>
                    <span>PANCARKAN AMARAN SERENTAK KE SEMUA TELEFON</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: ADD ASSEMBLY POINT -->
<div id="modal-add-point" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold uppercase text-white tracking-wider">Tambah Zon Berkumpul Baharu</h3>
            <button onclick="closeModal('modal-add-point')" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.assembly.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Nama Lokasi Zon</label>
                <input type="text" name="name" required placeholder="cth: Padang Futsal ADTEC"
                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Latitude</label>
                    <input type="number" step="any" name="latitude" required placeholder="3.966437"
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Longitude</label>
                    <input type="number" step="any" name="longitude" required placeholder="103.373426"
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Penerangan / Nota</label>
                <textarea name="description" rows="2" placeholder="Zon sandaran berhampiran pintu pagar utama"
                          class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2 font-mono-tech text-xs text-slate-300">
                <input type="checkbox" name="is_active" value="1" id="add-is-active" class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                <label for="add-is-active">Jadikan zon aktif utama serta-merta</label>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-xl uppercase tracking-wider text-sm transition">
                Simpan Zon Berkumpul
            </button>
        </form>
    </div>
</div>

<!-- MODAL: EDIT ASSEMBLY POINT -->
<div id="modal-edit-point" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold uppercase text-white tracking-wider">Kemaskini Zon Berkumpul</h3>
            <button onclick="closeModal('modal-edit-point')" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <form id="edit-point-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Nama Lokasi Zon</label>
                <input type="text" name="name" id="edit-point-name" required
                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Latitude</label>
                    <input type="number" step="any" name="latitude" id="edit-point-lat" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Longitude</label>
                    <input type="number" step="any" name="longitude" id="edit-point-lng" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Penerangan / Nota</label>
                <textarea name="description" id="edit-point-desc" rows="2"
                          class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2 text-white font-mono-tech text-sm focus:border-amber-500 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center gap-2 font-mono-tech text-xs text-slate-300">
                <input type="checkbox" name="is_active" value="1" id="edit-point-active" class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                <label for="edit-point-active">Tetapkan sebagai zon aktif utama</label>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-xl uppercase tracking-wider text-sm transition">
                Kemaskini Zon
            </button>
        </form>
    </div>
</div>

<!-- MODAL: SETTINGS -->
<div id="modal-settings" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold uppercase text-white tracking-wider flex items-center gap-2">
                <span>⚙️</span>
                <span>Konfigurasi Sistem Keselamatan</span>
            </h3>
            <button onclick="closeModal('modal-settings')" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Kod PIN Kecemasan Pantas</label>
                <input type="text" name="emergency_pin" value="{{ $emergencyPin }}" required maxlength="10"
                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-amber-400 font-bold tracking-widest text-lg font-mono-tech focus:border-amber-500 focus:outline-none">
                <p class="text-[11px] text-slate-500 font-mono-tech mt-1">Kod ini digunakan untuk log masuk pantas dan pintasan cemas tanpa kata laluan.</p>
            </div>

            <div class="flex items-center gap-2 font-mono-tech text-xs text-slate-300 pt-2">
                <input type="checkbox" name="siren_sound_enabled" value="1" id="siren-toggle" {{ $sirenEnabled ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-red-500">
                <label for="siren-toggle">Bunyikan Siren Amaran Audio secara automatik pada telefon pelajar</label>
            </div>

            <div class="p-3 bg-slate-950 rounded-lg border border-slate-800 text-xs font-mono-tech text-slate-400 space-y-1">
                <div class="text-slate-300 font-bold">Kunci VAPID Web Push:</div>
                <div class="truncate text-[10px] text-slate-500">{{ \App\Models\AdminSetting::get('vapid_public_key') }}</div>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl uppercase tracking-wider text-sm transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openTriggerModal(type) {
        const modal = document.getElementById('modal-trigger');
        const select = document.getElementById('alert-type-select');
        const titleInput = document.getElementById('alert-title-input');
        const msgInput = document.getElementById('alert-message-input');

        select.value = type;
        if (type === 'DRILL') {
            titleInput.value = 'LATIHAN PENGUNGSIAN KEBAKARAN (FIRE DRILL)';
            msgInput.value = 'Ini adalah latihan pengungsian bangunan kolej. Sila bergerak ke Padang ADTEC Kuantan sekarang untuk pengiraan kehadiran.';
        } else {
            titleInput.value = 'AMARAN PENGUNGSIAN BANGUNAN!';
            msgInput.value = 'Kecemasan dikesan! Sila keluar ke Padang ADTEC Kuantan dengan tenang dan ikut anak panah panduan pada skrin anda.';
        }
        openModal('modal-trigger');
    }

    function openAddPointModal() {
        openModal('modal-add-point');
    }

    function openEditPointModal(point) {
        document.getElementById('edit-point-name').value = point.name;
        document.getElementById('edit-point-lat').value = point.latitude;
        document.getElementById('edit-point-lng').value = point.longitude;
        document.getElementById('edit-point-desc').value = point.description || '';
        document.getElementById('edit-point-active').checked = !!point.is_active;

        document.getElementById('edit-point-form').action = `/admin/assembly-point/${point.id}`;
        openModal('modal-edit-point');
    }

    function openSettingsModal() {
        openModal('modal-settings');
    }
</script>
@endsection
