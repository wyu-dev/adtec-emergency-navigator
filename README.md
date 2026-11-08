# Sistem Amaran Pengungsian & Navigasi Kecemasan PWA (ADTEC Kuantan)

Sistem amaran kecemasan berasaskan **Progressive Web App (PWA)** dan dikuasakan oleh **Laravel 12/13**, direka khas untuk pengungsian pantas warga institut/kolej ke Zon Berkumpul (**Padang ADTEC Kuantan**) dengan panduan kompas dan Web Push Notification serentak.

Repositori GitHub: [wyu-dev/adtec-emergency-navigator](https://github.com/wyu-dev/adtec-emergency-navigator)

---

## 🌟 Ciri-Ciri Utama Sistem

### 1. Panel Kawalan Pegawai Keselamatan (Admin Dashboard)
- **Mod Dwi-Pengesahan (Dual-Auth):**
  - **Log Masuk Penuh:** Menggunakan Emel & Kata Laluan rasmi (`admin@adtec.edu.my` / `password123`).
  - **Akses Pintasan Kod PIN Pantas:** Masukkan 4-digit PIN (`9999`) terus dari skrin sentuh untuk mencetuskan amaran dalam saat kecemasan tanpa menaip panjang.
- **Pencetus Amaran Kecemasan Serentak:**
  - Pilihan mod: **Kecemasan Sebenar (Real Emergency)** atau **Latihan Pengungsian (Fire Drill)**.
  - Memancarkan Web Push Notification ke semua peranti pelajar yang berdaftar.
- **Butang Tamat Amaran (All Clear):**
  - Mematikan siren pada peranti pelajar dan menghantar notifikasi pengesahan selamat.
- **Pengurusan Zon Berkumpul Dinamik (Assembly Points):**
  - Titik lalai: **Padang ADTEC Kuantan** (Lat: `3.966437`, Lng: `103.373426`).
  - Boleh menambah, menyunting koordinat, dan menukar zon aktif utama secara langsung.
- **Log Audit & Sejarah Amaran:**
  - Merekodkan semua insiden, masa mula, masa selesai, dan pegawai yang mencetuskan amaran.

### 2. Aplikasi Klien / PWA Navigasi Pelajar
- **Panduan Arah Anak Panah Kompas Langsung:**
  - Mengintegrasikan gabungan sensor `AbsoluteOrientationSensor`, `deviceorientationabsolute`, `deviceorientation`, dan `webkitCompassHeading` (iOS).
  - Mengira sudut putaran anak panah (*relative bearing*) terus ke Padang ADTEC Kuantan.
- **Penjejakan Jarak GPS (Formula Haversine):**
  - Memaparkan baki jarak dalam meter (`m`) atau kilometer (`km`) serta ketepatan bacaan GPS.
- **Ambang Ketibaan (<15m):**
  - Paparan skrin penuh "✓ SAMPAI", getaran khas peranti, dan rekod ketibaan.
- **Siren Amaran Bunyi Automatik (Web Audio Synthesizer):**
  - Menghasilkan nada siren kecemasan autentik tanpa bergantung kepada fail audio luar; 100% berfungsi secara luar talian (*offline*).
  - Dilengkapi butang bisu (*mute toggle*).
- **Sokongan PWA Penuh:**
  - `manifest.json` dan `sw.js` (Service Worker) untuk pemasangan *Add to Home Screen*.
  - Langganan notifikasi telefon berkunci (*Web Push / VAPID Protocol*).

---

## 🔑 Maklumat Log Masuk Lalai

| Peranan | Jenis Akses | Kelayakan / Nilai |
| :--- | :--- | :--- |
| **Pegawai Keselamatan** | Emel & Kata Laluan | `admin@adtec.edu.my` / `password123` |
| **Pintasan Cemas** | Kod PIN Pantas | `9999` |

---

## 🚀 Panduan Memulakan Sistem (Local Development)

### 1. Keperluan Sistem
- PHP 8.2 / 8.3 / 8.4
- SQLite (atau MySQL)
- Composer

### 2. Pemasangan & Persediaan
```bash
# Salin konfigurasi environment (jika belum ada)
cp .env.example .env

# Jana kunci aplikasi
php artisan key:generate

# Jalankan migrasi dan data awal (seeder)
php artisan migrate --seed

# Jalankan pelayan pembangunan Laravel
php artisan serve
```

Akses sistem di pelayar:
- **Paparan Navigasi Pelajar (PWA):** `http://localhost:8000/`
- **Panel Kawalan Admin:** `http://localhost:8000/login` atau `http://localhost:8000/admin`

---

## 📁 Struktur Projek

```
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php         # Pengurusan amaran, titik berkumpul & tetapan
│   │   ├── AuthController.php          # Dwi-log masuk (Emel/Password & Quick PIN)
│   │   └── EvacuationController.php    # Paparan navigasi pelajar & API status kecemasan
│   ├── Models/
│   │   ├── AdminSetting.php            # Penyimpanan tetapan sistem & VAPID keys
│   │   ├── AssemblyPoint.php           # Model koordinat zon berkumpul (ADTEC Kuantan)
│   │   ├── EmergencyAlert.php          # Status amaran aktif & log audit
│   │   └── PushSubscription.php        # Rekod langganan Web Push peranti
│   └── Services/
│       └── WebPushService.php          # Pengendali penghantaran Web Push VAPID
├── database/
│   ├── migrations/                     # Skema pangkalan data
│   └── seeders/DatabaseSeeder.php      # Data lalai admin, koordinat padang & kunci VAPID
├── public/
│   ├── icons/                          # Ikon PWA maskable
│   ├── manifest.json                   # Web App Manifest
│   └── sw.js                           # Service worker PWA & Push handler
├── resources/views/
│   ├── admin/
│   │   ├── dashboard.blade.php         # Papan pemuka pengurusan amaran
│   │   └── login.blade.php             # Dwi-tab log masuk admin & keypad PIN
│   ├── client/
│   │   └── navigator.blade.php         # Antara muka kompas & navigasi kecemasan PWA
│   └── layouts/admin.blade.php         # Susun atur Tailwind CSS admin
└── tests/Feature/
    └── EvacuationSystemTest.php        # Ujian automasi sistem & keselamatan
```

---

## 🛡️ Hak Cipta & Lesen
Dibangunkan untuk Pengurusan Kecemasan ADTEC JTM Kuantan.
Lesen: [MIT](LICENSE)
