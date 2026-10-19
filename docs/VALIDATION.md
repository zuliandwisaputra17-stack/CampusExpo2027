# Verifikasi desain biru muda

Dilakukan 19 Oktober 2026 pada PHP 8.4.24, Node.js 24.19.0, SQLite, dan Chromium 154.

| Pemeriksaan | Hasil |
|---|---|
| `php artisan test` | 27 tes lulus, 176 assertions |
| `vendor/bin/pint --test` | Lulus |
| `npm run typecheck` | Lulus |
| `npm run build` | Lulus; React, CSS, dan manifest produksi tersedia |
| `npm run test:browser` | 65 pemeriksaan lulus |
| `npm run test:a11y` | Tidak ada pelanggaran axe WCAG A/AA terdeteksi pada homepage 1440 dan 390 px |

Pengujian browser mencakup panorama kampus melengkung, jeda/putar, reduced motion, anggota polaroid dengan drag/keyboard/tombol, putaran daftar panjang, hasil filter kosong/satu/dua anggota, URL filter yang dapat dibagikan, dan direktori tanpa JavaScript. Pencarian, jurusan/prodi, FAQ, lightbox, gambar, navigasi HP, login/logout, daftar CMS, urutan jadwal, CRUD kampus, serta CSRF juga diperiksa.

Tautan Instagram dan LinkedIn diuji dari pembuatan anggota lewat form admin hingga tampil sebagai tautan pada profil publik. Pengujian Laravel memeriksa penyimpanan, pembaruan, pengosongan tautan, kolom opsional, dan penolakan skema URL yang tidak aman. Tidak diperlukan perubahan skema database.

Countdown diuji sebelum mulai, saat mulai, tepat akhir, dan setelah akhir acara dengan zona waktu WIB. Viewport 360, 390, dan 430 px tidak menunjukkan overflow horizontal. Screenshot desktop/HP tersedia di `/workspace/artifacts` pada environment pengujian.

Paket `downloads/CampusExpo2027-Update-Biru.zip` dibuat dari daftar file eksplisit serta manifest aset produksi. Integritas arsip diperiksa; tidak menyertakan `.env`, database, vendor, composer lockfile, storage, atau foto unggahan pengguna.

Batas pemeriksaan: pengujian otomatis memakai SQLite dan Chromium di cloud. MySQL Laragon, browser Windows, Safari/Firefox, dan perangkat fisik belum dijalankan dari environment ini. Tidak ada migrasi baru. Akun dan data di komputer pengguna tidak diubah oleh task ini; pembaruan lokal diterapkan dengan menyalin paket sesuai [panduan](DESIGN-UPDATE.md).
