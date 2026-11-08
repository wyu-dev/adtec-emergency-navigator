# Pelan Reka Bentuk Sistem Amaran Pengungsian & Navigasi Kecemasan Berasaskan Web (PWA)

Dokumen ini merangkumkan seni bina, aliran kerja (*workflow*), serta spesifikasi teknikal bagi membina sistem amaran kecemasan berasaskan **Progressive Web App (PWA)** yang membolehkan pegawai pengungsian menghantar notifikasi kecemasan, memainkan amaran audio ringkas, serta membimbing pelajar ke zon berkumpul menggunakan panduan navigasi anak panah (*directional arrow*).

---

## 1. Seni Bina Sistem (Architecture Overview)

```
[Pegawai / Admin Dashboard]
           │
           ▼ (HTTPS POST / API Trigger)
    [Pelayan / Backend API]
           │
           ▼ (Web Push Protocol / VAPID)
 [Push Service (FCM / Mozilla / Apple)]
           │
           ▼
[Telefon Pelajar: Service Worker PWA]
  ├─ 1. Papar Web Push Notification (Teks + Getaran)
  └─ 2. Butang Tindakan: "Buka Panduan & Navigasi"
           │
           ▼ (Pelajar ketik notifikasi / buka PWA)
[Halaman Penuh PWA (Evacuation Screen)]
  ├─ 3. Mainkan Audio Amaran Ringkas (Autoplay dibenarkan)
  ├─ 4. Senarai Langkah Keselamatan (SOP Ringkas)
  └─ 5. Mod Navigasi Kompas (GPS + Sensor Arah)
```

---

## 2. Aliran Kerja Pengguna (User Flow)

### Fasa 1: Prapendaftaran & Kebenaran (Semasa Hari Biasa)
1. Pelajar mengimbas kod QR di papan kenyataan kolej atau semasa pendaftaran Wi-Fi.
2. Web dibuka dan memaparkan gesaan untuk memasang aplikasi (*Add to Home Screen*).
3. PWA meminta tiga kebenaran penting:
   * **Notifikasi Web (Web Push):** Menerima amaran serentak.
   * **Lokasi (Geolocation):** Menentukan jarak ke zon berkumpul.
   * **Sensor Gerakan/Orientasi (Device Motion/Compass):** Menentukan arah panah navigasi.
4. Token pendaftaran peranti (*Subscription Endpoint*) disimpan di pangkalan data pelayan.

### Fasa 2: Pengaktifan Kecemasan (Semasa Kejadian)
1. Pegawai keselamatan atau pengungsian menekan butang kecemasan pada panel admin web.
2. Pelayan memicu *Web Push* serentak ke semua peranti berdaftar dalam premis.
3. Telefon pintar pelajar berbunyi mengikut nada dering amaran peranti serta menghasilkan corak getaran kecemasan.
4. Notifikasi kekal pada skrin berkunci (*lock screen*) dengan label amaran kecemasan.

### Fasa 3: Evakuasi & Navigasi (Tindakan Pelajar)
1. Pelajar mengetik notifikasi.
2. Tindakan sentuhan mengangkat sekatan pelayar (*user gesture restriction*), membolehkan fail audio siren ringkas atau arahan suara dimainkan terus secara automatik.
3. Paparan keselamatan menunjukkan tindakan segera (cth: jangan guna lif, rendahkan badan jika ada asap).
4. Pelajar menekan butang navigasi; grafik anak panah berpusing secara langsung mengikut pergerakan fizikal telefon, membimbing pelajar menuju terus ke titik berkumpul (*Assembly Point*).

---

## 3. Kod Prototaip Pelaksanaan

### A. Konfigurasi Service Worker (`service-worker.js`)
Mengendalikan penerimaan mesej *push* di latar belakang dan memaparkan notifikasi bersama getaran.

```javascript
self.addEventListener('push', function(event) {
  let payload = {
    title: 'AMARAN PENGUNGSIAN BANGUNAN!',
    message: 'Kecemasan dikesan. Sila keluar ke zon berkumpul dengan tenang.',
    targetUrl: '/evacuate.html'
  };

  if (event.data) {
    try {
      payload = event.data.json();
    } catch (e) {
      payload.message = event.data.text();
    }
  }

  const options = {
    body: payload.message,
    icon: '/icons/warning-192.png',
    badge: '/icons/badge-72.png',
    vibrate: [500, 250, 500, 250, 500], // Corak getaran amaran kecemasan
    tag: 'emergency-alert',
    renotify: true,
    requireInteraction: true, // Kekal pada skrin sehingga ditekan
    actions: [
      { action: 'navigate', title: '🧭 Buka Navigasi' }
    ],
    data: { url: payload.targetUrl || '/evacuate.html' }
  };

  event.waitUntil(
    self.registration.showNotification(payload.title, options)
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  const targetUrl = event.notification.data.url;

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
      // Fokus tetingkap sedia ada atau buka tetingkap baharu
      for (let client of windowClients) {
        if (client.url.includes(targetUrl) && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl);
      }
    })
  );
});
```

---

### B. Halaman Paparan Kecemasan & Navigasi (`evacuate.html`)
Menguruskan audio amaran ringkas, senarai SOP kecemasan, serta pengiraan arah panah kompas.

```html
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panduan Pengungsian Bangunan</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background-color: #0f172a;
      color: #f8fafc;
      margin: 0;
      padding: 16px;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      box-sizing: border-box;
    }
    .banner {
      background-color: #dc2626;
      width: 100%;
      text-align: center;
      padding: 12px;
      font-weight: 700;
      font-size: 1.1rem;
      border-radius: 8px;
      margin-bottom: 16px;
      animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.8; }
    }
    .sop-box {
      background-color: #1e293b;
      padding: 16px;
      border-radius: 8px;
      width: 100%;
      max-width: 400px;
      margin-bottom: 20px;
      box-sizing: border-box;
    }
    .sop-box h3 { margin-top: 0; color: #f87171; }
    .sop-box ul { padding-left: 20px; margin-bottom: 0; }
    .arrow-container {
      width: 180px;
      height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 20px 0;
    }
    #direction-arrow {
      width: 0; 
      height: 0; 
      border-left: 35px solid transparent;
      border-right: 35px solid transparent;
      border-bottom: 90px solid #22c55e;
      transition: transform 0.15s ease-out;
      filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.5));
    }
    .stats {
      font-size: 1.1rem;
      text-align: center;
      margin-bottom: 16px;
    }
    .btn {
      background-color: #2563eb;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 1rem;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="banner">⚠️ KECEMASAN: SILA PINDAH SEGERA</div>

  <div class="sop-box">
    <h3>Langkah Keselamatan Pantas:</h3>
    <ul>
      <li>Jangan gunakan lif; gunakan tangga kecemasan.</li>
      <li>Bertenang dan jangan berlari berebut-rebut.</li>
      <li>Ikut arah anak panah hijau di bawah menuju zon berkumpul.</li>
    </ul>
  </div>

  <div class="arrow-container">
    <div id="direction-arrow"></div>
  </div>

  <div class="stats">
    <div>Arah ke Zon Berkumpul</div>
    <div id="distance-label">Mengesan jarak...</div>
  </div>

  <button id="btn-request" class="btn" onclick="initSensors()">Mulakan Navigasi Sensor</button>

  <audio id="alarm-sound" src="/audio/alarm-short.mp3" preload="auto"></audio>

  <script>
    // Koordinat Zon Berkumpul (Assembly Point) - Ubah mengikut tapak sebenar
    const TARGET_LAT = 3.8167;
    const TARGET_LNG = 103.3333;

    let currentLat = null;
    let currentLng = null;
    let userHeading = 0;

    // Main audio automatik sebaik halaman diakses
    window.addEventListener('DOMContentLoaded', () => {
      const audio = document.getElementById('alarm-sound');
      audio.play().catch(err => {
        console.warn("Autoplay ditolak pelayar sehingga interaksi pertama:", err);
      });
      initGPS();
    });

    function initGPS() {
      if ('geolocation' in navigator) {
        navigator.geolocation.watchPosition(
          (pos) => {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            updateNavigation();
          },
          (err) => console.error("Ralat GPS:", err),
          { enableHighAccuracy: true, maximumAge: 1000 }
        );
      }
    }

    function initSensors() {
      document.getElementById('btn-request').style.display = 'none';

      // Sokongan khas iOS 13+ untuk kebenaran sensor kompas
      if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
        DeviceOrientationEvent.requestPermission()
          .then(response => {
            if (response === 'granted') {
              window.addEventListener('deviceorientation', handleOrientation, true);
            }
          })
          .catch(console.error);
      } else {
        window.addEventListener('deviceorientationabsolute', handleOrientation, true);
        window.addEventListener('deviceorientation', handleOrientation, true);
      }
    }

    function handleOrientation(event) {
      if (event.webkitCompassHeading) {
        // Untuk peranti iOS
        userHeading = event.webkitCompassHeading;
      } else if (event.alpha !== null) {
        // Untuk peranti Android
        userHeading = 360 - event.alpha;
      }
      updateNavigation();
    }

    function calculateBearing(lat1, lon1, lat2, lon2) {
      const toRad = deg => (deg * Math.PI) / 180;
      const toDeg = rad => (rad * 180) / Math.PI;

      const dLon = toRad(lon2 - lon1);
      const y = Math.sin(dLon) * Math.cos(toRad(lat2));
      const x = Math.cos(toRad(lat1)) * Math.sin(toRad(lat2)) -
                Math.sin(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.cos(dLon);

      const brng = Math.atan2(y, x);
      return (toDeg(brng) + 360) % 360;
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
      const R = 6371e3; // Radius bumi dalam meter
      const toRad = deg => (deg * Math.PI) / 180;
      const dLat = toRad(lat2 - lat1);
      const dLon = toRad(lon2 - lon1);

      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
      return Math.round(R * c);
    }

    function updateNavigation() {
      if (currentLat === null || currentLng === null) return;

      const targetBearing = calculateBearing(currentLat, currentLng, TARGET_LAT, TARGET_LNG);
      const distance = calculateDistance(currentLat, currentLng, TARGET_LAT, TARGET_LNG);

      document.getElementById('distance-label').innerText = `Baki Jarak: ${distance} meter`;

      // Kira sudut putaran relatif anak panah terhadap orientasi telefon
      const arrowRotation = (targetBearing - userHeading + 360) % 360;
      document.getElementById('direction-arrow').style.transform = `rotate(${arrowRotation}deg)`;
    }
  </script>
</body>
</html>
```

---

## 4. Kelebihan & Batasan Teknikal Pendekatan PWA

### Kelebihan
1. **Penyebaran Pantas Tanpa App Store:** Pelajar tidak perlu melalui proses memuat turun fail berpuluh megabait dari Google Play atau App Store.
2. **Kemas Kini Automatik:** Sebarang pengemaskinian laluan zon berkumpul boleh diubah terus di pelayan tanpa pengguna perlu mengemas kini aplikasi.
3. **Penyatuan Visual & Navigasi:** Menyediakan panduan berpusat—bukan sekadar teks notifikasi, malah memaparkan arah tepat secara masa nyata (*real-time orientation*).

### Batasan & Strategi Mitigasi
* **Pintasan Mod Senyap (*Silent/DND Mode*):** PWA tidak mempunyai hak untuk memintas mod senyap peranti.
  * *Mitigasi:* Gandingkan butang admin dengan siren/loceng fizikal bangunan untuk amaran bunyi utama; PWA bertindak sebagai penyampai maklumat dan navigasi visual.
* **Ketepatan GPS Dalam Bangunan (*Indoor Dead Zone*):** Isyarat satelit GPS mungkin lemah di tingkat bawah bangunan tebal.
  * *Mitigasi:* Sediakan tab sokongan yang memaparkan gambar rajah pelan lantai tingkat (*floor plan diagram*) yang menunjukkan laluan tangga kecemasan sekiranya isyarat GPS dikesan hilang.