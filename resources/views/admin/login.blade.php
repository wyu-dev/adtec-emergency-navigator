@extends('layouts.admin')

@section('title', 'Log Masuk Pegawai Keselamatan')

@section('content')
<div class="max-w-md mx-auto my-10">
    <div class="bg-slate-900 border border-slate-800 rounded-xl shadow-2xl overflow-hidden">
        <!-- Header Banner -->
        <div class="p-6 bg-gradient-to-b from-slate-800 to-slate-900 border-b border-slate-800 text-center">
            <div class="inline-flex p-3 bg-red-950/60 border border-red-800/80 rounded-full mb-3 text-red-500 text-2xl">
                🛡️
            </div>
            <h2 class="text-2xl font-bold tracking-wider uppercase text-white">Panel Kawalan Kecemasan</h2>
            <p class="text-xs text-slate-400 font-mono-tech mt-1">Sila sahkan identiti untuk mengurus amaran & titik pengungsian</p>
        </div>

        <!-- Tabs Selector -->
        <div class="flex border-b border-slate-800 text-center text-sm font-semibold font-mono-tech">
            <button id="tab-btn-email" onclick="switchTab('email')" class="flex-1 py-3 px-4 text-red-400 border-b-2 border-red-500 bg-slate-900/50 hover:bg-slate-800/50 transition">
                📧 Emel & Kata Laluan
            </button>
            <button id="tab-btn-pin" onclick="switchTab('pin')" class="flex-1 py-3 px-4 text-slate-400 border-b-2 border-transparent hover:text-slate-200 hover:bg-slate-800/50 transition flex items-center justify-center gap-1.5">
                <span>⚡ Kod PIN Pantas</span>
            </button>
        </div>

        <!-- Form 1: Email & Password -->
        <div id="tab-email" class="p-6 space-y-4">
            <form action="{{ route('login.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Alamat Emel</label>
                    <input type="email" name="email" value="{{ old('email', 'admin@adtec.edu.my') }}" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-red-500 font-mono-tech text-sm transition">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-slate-400 font-mono-tech mb-1">Kata Laluan</label>
                    <input type="password" name="password" value="password123" required
                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-slate-200 focus:outline-none focus:border-red-500 font-mono-tech text-sm transition">
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 font-mono-tech">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-950 border-slate-800 text-red-600 focus:ring-0">
                        <span>Ingat peranti ini</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg tracking-wider uppercase transition shadow-lg shadow-red-900/30">
                    Log Masuk Rasmi
                </button>
            </form>

            <div class="p-3 rounded bg-slate-950/60 border border-slate-800/70 text-xs text-slate-400 font-mono-tech">
                <span class="text-red-400 font-bold">Akaun Contoh:</span> admin@adtec.edu.my / password123
            </div>
        </div>

        <!-- Form 2: Quick PIN -->
        <div id="tab-pin" class="p-6 space-y-4 hidden">
            <div class="text-center">
                <div class="text-xs text-amber-400 font-mono-tech mb-2">⚡ PINTASAN KECEMASAN PANTAS</div>
                <p class="text-xs text-slate-400">Masukkan 4 digit PIN kecemasan untuk melangkau kata laluan.</p>
            </div>

            <form action="{{ route('login.pin') }}" method="POST" id="pin-form" class="space-y-4">
                @csrf
                <div class="flex justify-center">
                    <input type="password" name="pin" id="pin-input" maxlength="6" autofocus placeholder="••••"
                           class="w-48 text-center tracking-[1em] text-2xl font-bold bg-slate-950 border-2 border-slate-800 rounded-xl px-4 py-3 text-amber-400 focus:outline-none focus:border-amber-500 font-mono-tech transition">
                </div>

                <!-- Touch keypad for mobile/touchscreen kiosk -->
                <div class="grid grid-cols-3 gap-2 max-w-[220px] mx-auto pt-2">
                    @for($i = 1; $i <= 9; $i++)
                        <button type="button" onclick="appendPin('{{ $i }}')"
                                class="py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-lg rounded-lg border border-slate-700 active:scale-95 transition">
                            {{ $i }}
                        </button>
                    @endfor
                    <button type="button" onclick="clearPin()"
                            class="py-2.5 bg-red-950/60 hover:bg-red-900/60 text-red-300 font-bold text-sm rounded-lg border border-red-900/50 active:scale-95 transition">
                        CLR
                    </button>
                    <button type="button" onclick="appendPin('0')"
                            class="py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-lg rounded-lg border border-slate-700 active:scale-95 transition">
                        0
                    </button>
                    <button type="button" onclick="backspacePin()"
                            class="py-2.5 bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-sm rounded-lg border border-slate-700 active:scale-95 transition">
                        ⌫
                    </button>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-amber-600 hover:bg-amber-500 text-slate-950 font-bold rounded-lg tracking-wider uppercase transition shadow-lg shadow-amber-900/30">
                    Sahkan Kod PIN
                </button>
            </form>

            <div class="p-3 rounded bg-slate-950/60 border border-slate-800/70 text-center text-xs text-slate-400 font-mono-tech">
                <span class="text-amber-400 font-bold">PIN Lalai:</span> 9999
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchTab(tab) {
        const emailTab = document.getElementById('tab-email');
        const pinTab = document.getElementById('tab-pin');
        const emailBtn = document.getElementById('tab-btn-email');
        const pinBtn = document.getElementById('tab-btn-pin');

        if (tab === 'email') {
            emailTab.classList.remove('hidden');
            pinTab.classList.add('hidden');
            emailBtn.className = 'flex-1 py-3 px-4 text-red-400 border-b-2 border-red-500 bg-slate-900/50 hover:bg-slate-800/50 transition';
            pinBtn.className = 'flex-1 py-3 px-4 text-slate-400 border-b-2 border-transparent hover:text-slate-200 hover:bg-slate-800/50 transition flex items-center justify-center gap-1.5';
        } else {
            emailTab.classList.add('hidden');
            pinTab.classList.remove('hidden');
            pinBtn.className = 'flex-1 py-3 px-4 text-amber-400 border-b-2 border-amber-500 bg-slate-900/50 hover:bg-slate-800/50 transition flex items-center justify-center gap-1.5';
            emailBtn.className = 'flex-1 py-3 px-4 text-slate-400 border-b-2 border-transparent hover:text-slate-200 hover:bg-slate-800/50 transition';
            document.getElementById('pin-input').focus();
        }
    }

    function appendPin(num) {
        const input = document.getElementById('pin-input');
        if (input.value.length < 6) {
            input.value += num;
        }
    }

    function clearPin() {
        document.getElementById('pin-input').value = '';
    }

    function backspacePin() {
        const input = document.getElementById('pin-input');
        input.value = input.value.slice(0, -1);
    }
</script>
@endsection
