# Verifikasi instance development

Dilakukan 18 Oktober 2026 pada PHP 8.4.24, Node.js 24.19.0, SQLite, dan Chromium 154.

| Pemeriksaan | Hasil |
|---|---|
| `php artisan test --compact` | 24 tes lulus, 143 assertions |
| `vendor/bin/pint --test` | Lulus |
| `npm run build` | Lulus; aset produksi tersedia |
| `npm run test:browser` | 49 pemeriksaan lulus |
| `npm run test:a11y` | Tidak ada pelanggaran axe WCAG A/AA yang terdeteksi pada homepage 1440 dan 390 px |
| `scripts/setup.sh` dijalankan ulang | Lulus; migration aman dan konten yang sudah ada dipertahankan |
| `php artisan config:cache`, `route:cache`, `view:cache` | Lulus; homepage tetap merespons HTTP 200 |
| `/`, `/admin/login`, `/up` | HTTP 200 |

Pemeriksaan browser mencakup klik kampus → filter anggota → URL yang dapat dibagikan, pencarian, empty state, reset, filter jurusan/prodi, kontrol carousel dan keyboard, FAQ, galeri, semua aset gambar, menu mobile, login/logout, seluruh daftar CMS, pengurutan jadwal, create/edit/delete kampus hingga hasil publik, dan penolakan request tanpa CSRF. Countdown diuji pada waktu tetap sebelum mulai, saat mulai, tepat akhir, dan setelah akhir dalam timezone WIB.

Pada viewport 360, 390, dan 430 px tidak ditemukan overflow horizontal dokumen. Screenshot development berada di `/workspace/artifacts` pada environment ini.

Belum diverifikasi: MySQL/MariaDB production, SMTP sungguhan, Safari/Firefox/Edge, perangkat fisik, target Lighthouse, hosting publik, DNS/TLS, backup dan pemulihannya. Foto, nama sekolah, daftar perwakilan, dan jadwal saat ini adalah contoh. Website belum dideploy ke domain production.

Konfigurasi environment `install_script` dan `start_skill` sudah disimpan sebagai draft. Penyimpanan draft tidak mempublikasikan snapshot; review dan simpan di pengaturan environment, lalu Publish jika ingin mengaktifkannya untuk task berikutnya.
