# Pembaruan desain biru muda

Pembaruan 19 Oktober 2026 mencakup tema biru muda, panorama kampus melengkung dari referensi Tilted Grid Hero, carousel anggota polaroid dari referensi Polaroid Line Carousel, dan pengisian Instagram/LinkedIn melalui admin. Foto dan konten tetap berasal dari CMS.

## Memasang ke proyek Laragon yang sudah berjalan

1. Unduh [CampusExpo2027-Update-Biru.zip](../downloads/CampusExpo2027-Update-Biru.zip). Di GitHub, klik **Download raw file** jika file ZIP terbuka sebagai halaman.
2. Hentikan `php artisan serve` dengan **Ctrl+C**. Jika ada `npm run dev` yang sedang berjalan, hentikan juga dengan Ctrl+C.
3. Ekstrak ZIP ke folder sementara. Salin **semua isi hasil ekstrak** ke folder proyek yang berisi `artisan`:

   ```text
   C:\laragon\www\CampusExpo2027-campus-expo-website
   ```

4. Pilih **Replace the files in the destination** untuk file desain versi lama. Jangan membuat folder proyek bersarang. File `resources`, `config`, dan `public` harus bergabung dengan folder yang sudah ada.
5. Buka Terminal Laragon atau CMD yang biasa digunakan untuk menjalankan proyek:

   ```bat
   cd /d C:\laragon\www\CampusExpo2027-campus-expo-website
   php artisan config:clear
   php artisan view:clear
   php artisan serve
   ```

6. Buka `http://127.0.0.1:8000`, lalu tekan **Ctrl+F5**.

ZIP pembaruan sudah menyertakan `public/build`, sehingga tidak perlu memasang ulang Composer/npm untuk melihat desain. File `.env`, `composer.json`, `composer.lock`, `database`, `vendor`, `storage`, dan `public/storage` tidak dimuat dalam paket. Pengaturan MySQL, akun admin, konten, dan foto unggahan tetap memakai yang ada di komputer. Tidak ada migrasi baru dan tidak perlu menjalankan `migrate:fresh` atau seeder.

Jika masih muncul desain lama setelah menghentikan Vite, periksa `public/hot`. File ini dibuat oleh Vite dev server; hapus hanya file `public/hot` yang tertinggal agar Laravel kembali membaca aset produksi.

## Menambahkan media sosial anggota

Buka **Portal Panitia → Anggota → Edit**. Isi:

- **Tautan Instagram (opsional):** `https://www.instagram.com/username/`
- **Tautan LinkedIn (opsional):** `https://www.linkedin.com/in/username/`

Klik **Simpan Perubahan**. Di halaman umum, cari nama anggota. Tautannya muncul pada profil di bawah polaroid yang sedang dipilih. Kolom kosong menyembunyikan tautan; mengosongkan kolom lalu menyimpan menghapus tautan sebelumnya. Tidak ada akun sosial contoh yang otomatis dipasang pada profil seseorang.

## Mengembangkan kode desain

React dipasang sebagai bagian interaktif dari halaman Blade. Data kampus dan anggota diserialisasikan secara aman oleh Laravel; pencarian dan filter tetap memakai data edisi acara yang aktif.

- `resources/css/sky.css`: palet biru muda dan desain responsif halaman publik serta admin.
- `resources/css/base.css`: struktur dan tata letak dasar.
- `resources/js/components/ui/tilted-grid-hero.tsx`: gambar kampus disusun dari 16 potongan per gambar di lintasan silinder.
- `resources/js/components/ui/polaroid-line-carousel.tsx`: gerak foto anggota, drag, keyboard, navigasi, dan profil sosial.
- `resources/js/showcases.tsx`: menghubungkan komponen React dengan halaman Laravel dan filter anggota.
- `resources/views/home.blade.php`: konten halaman dan direktori tanpa JavaScript.
- `config/cms.php`: definisi form CMS, termasuk URL sosial anggota.

Struktur komponen mengikuti shadcn dengan direktori `resources/js/components/ui`, karena kode frontend Laravel berada di `resources/js`. Alias `@/components/ui` mengarah ke direktori tersebut; `components.json`, `tsconfig.json`, Vite React, dan Tailwind 4 sudah dikonfigurasi. Tidak perlu membuat proyek React terpisah atau mengulang inisialisasi shadcn. Tailwind dimuat tanpa preflight agar form Blade tetap konsisten.

Setelah mengunduh kode sumber dari GitHub, atau setelah mengedit CSS/TSX/JS:

```bat
npm ci
npm run typecheck
npm run build
php artisan view:clear
```

Untuk pengembangan dengan pembaruan otomatis, jalankan `npm run dev` di terminal kedua sambil `php artisan serve` tetap berjalan. Produksi/ZIP menggunakan hasil `npm run build`.

Pengemasan pembaruan: `python3 scripts/package-design-update.py`. Script mengambil daftar file eksplisit serta aset pada manifest build dan memverifikasi isi ZIP. Jalankan ulang setelah perubahan desain dan build.
