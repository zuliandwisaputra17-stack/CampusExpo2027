# Campus Expo 2027

Website Campus Expo berbahasa Indonesia dengan halaman publik single page dan CMS panitia di `/admin`. Dibangun dengan **Laravel 12, Blade, Vite, CSS, JavaScript, dan Eloquent**. Admin dibuat khusus untuk kebutuhan PRD. Hero menggunakan alternatif **CSS 3D** sehingga tidak membutuhkan WebGL.

## Fitur

- Hero perspektif mengikuti pointer desktop; dukungan reduced motion, navigasi sticky, dan menu mobile.
- Countdown WIB dengan status upcoming, live, dan selesai; timeline kegiatan dengan status otomatis dan tab per hari.
- Campus Explorer: drag desktop, swipe mobile, kontrol tombol, dan keyboard.
- Klik kampus mengarahkan ke anggota serta memilih filter; URL `/?campus=universitas-indonesia#anggota` dapat dibagikan.
- Pencarian anggota, filter kampus, jurusan, program studi, reset, empty state, serta tautan website kampus di tab baru.
- Lokasi, Google Maps link dan embed opsional, galeri lightbox, partner, FAQ, kontak, dan statistik.
- CMS edisi acara, kampus, anggota, jadwal, galeri, FAQ, partner, serta pengaturan sekolah, logo, favicon, SEO, kontak, dan statistik.
- CRUD dengan validasi, pencarian, filter status/edisi, pagination, sorting, dan pengurutan jadwal melalui drag atau tombol naik/turun.
- Satu edisi aktif; anggota dan jadwal publik mengikuti edisi tersebut. Kampus dan anggota menggunakan soft delete.
- Login khusus admin, remember me, reset kata sandi, pembatasan percobaan login, CSRF, escaping, authorization, dan header keamanan.
- Unggahan JPEG/PNG/WebP divalidasi maksimal 5 MB, nama acak, lalu diubah menjadi WebP. Foto anggota dicrop tengah ke 400 × 500. Logo/favicon maksimal 1 MB. Preview tersedia sebelum penyimpanan.
- SEO server-rendered, Open Graph, canonical, dan structured data EducationEvent. Aset lokal, gambar lazy-load, tanpa ketergantungan CDN ketika halaman dibuka.

## Jalankan lokal

Diperlukan PHP **8.4+** (sesuai versi pada lockfile), Composer 2, Node.js 22+/24, serta ekstensi ctype, curl, dom, fileinfo, gd dengan WebP, mbstring, pdo, tokenizer, xml, xmlreader, xmlwriter, zip, dan driver SQLite/MySQL. SQLite dipakai secara default agar aplikasi bisa langsung dicoba. MySQL/MariaDB dapat dipilih melalui `.env`.

```bash
./scripts/setup.sh
php artisan expo:admin panitia@sekolah.sch.id --name="Panitia Sekolah"
./scripts/dev.sh
```

Perintah akun meminta kata sandi secara tersembunyi, minimal 12 karakter. Tidak ada registrasi publik maupun password tetap dalam repository. Perintah yang sama dapat memperbarui kata sandi akun yang sudah ada.

Halaman publik: `/`. Portal panitia: `/admin` atau `/admin/login`. Server development menggunakan port 8000; ubah melalui `PORT=... ./scripts/dev.sh`.

`setup.sh` memasang dependencies sesuai lockfile, membuat `.env` dan app key jika belum ada, menjalankan migration, membuat storage link, lalu membangun aset. Konten contoh hanya ditambahkan apabila data edisi dan pengaturan masih kosong; konten yang sudah ada tidak ditimpa. Gunakan `./scripts/setup.sh --no-demo` untuk database kosong tanpa seeder.

Untuk hot reload aset, jalankan `npm run dev` pada terminal terpisah. Gunakan `npm run build` setelah mengubah CSS/JS bila tidak memakai Vite dev server.

### Di environment cloud ini

PHP disiapkan tanpa akses root di `/workspace/tooling/bin/php`, Composer di `/workspace/tooling/composer.phar`. Script setup dan dev otomatis memakai runtime tersebut bila PHP sistem tidak tersedia. Untuk perintah manual:

```bash
export PATH=/workspace/tooling/bin:$PATH
```

Bootstrap opsional untuk image Debian cloud baru: `./scripts/bootstrap-cloud-php.sh`. Script menggunakan metadata APT bertanda tangan dan checksum resmi Composer. Paket Composer pada lockfile menggunakan arsip commit GitHub lewat `codeload.github.com` karena endpoint `api.github.com` dibatasi pada environment ini; versi dan ref commit tetap dikunci.

Satu akun development dengan kata sandi acak telah dibuat pada instance ini. Detailnya berada di `storage/app/private/admin-login.txt` (diabaikan Git, tidak dapat diakses dari web). Ganti akun tersebut atau buat akun sekolah melalui command di atas sebelum produksi. Pada checkout baru, buat akun sendiri melalui `expo:admin`.

## Alur panitia

1. Login ke `/admin`.
2. Buka **Pengaturan Website**: ganti nama sekolah, kontak, statistik, SEO, logo, dan favicon. Isi kontak asli agar tautan kontak tampil.
3. Buka **Edisi Acara**: isi tanggal mulai/selesai dalam WIB, venue, alamat, deskripsi, hero, dan foto sekolah. Centang aktif pada satu edisi. Mengaktifkan edisi lain otomatis menonaktifkan edisi sebelumnya.
4. Tambahkan **Kampus**, foto, logo, kota, serta URL resmi.
5. Tambahkan **Anggota**, pilih edisi dan kampus, lalu isi prodi dan foto. Data nonaktif tidak muncul di publik.
6. Tambahkan **Jadwal** untuk edisi terkait. Gunakan **Atur urutan** untuk menggeser urutan kegiatan dalam tanggal yang sama.
7. Isi galeri, FAQ, dan partner. Data tanpa edisi pada modul tersebut ditampilkan lintas edisi.
8. Buka website publik untuk memeriksa hasil.

Tombol hapus kampus/anggota melakukan soft delete. Pemulihan saat ini dilakukan melalui Eloquent (`withTrashed()->find($id)->restore()`); UI pemulihan belum disediakan. Edisi aktif atau edisi berisi anggota tidak dapat dihapus, agar arsip tetap terjaga.

Semua identitas, foto, dan jadwal awal adalah **contoh**. Baca [asal dan ketentuan aset demo](docs/ASSETS.md). Ganti foto dengan aset milik sekolah atau berlisensi sebelum publikasi.

## Pengujian

```bash
php artisan test
vendor/bin/pint --test
npm run build
npm run test:a11y  # memerlukan server development dan Chromium
```

Pengujian fitur memakai SQLite in-memory, terpisah dari database development. Mencakup halaman kosong/aktif, auth, authorization, CRUD, unggahan gambar, XSS/URL validation, pergantian edisi, relasi, countdown, pengurutan jadwal, dan konfigurasi sekolah.

Pengujian browser memerlukan server development aktif dan Chromium:

```bash
CHROMIUM_PATH=/usr/bin/chromium npm run test:browser
```

Runner membuat akun/data sementara pada database development lalu membersihkannya. Jalankan hanya terhadap instance development. Gunakan `TEST_BASE_URL` jika port server berbeda. `PHP_BINARY` dapat menunjuk executable PHP. Bukti screenshot ditulis ke `/workspace/artifacts` pada cloud ini. Chromium tersedia pada instance; pada mesin lain pasang Chromium atau sesuaikan `CHROMIUM_PATH`.

Browser yang telah diperiksa di environment ini: Chromium desktop serta viewport mobile 360, 390, dan 430 px. Safari, Firefox, perangkat fisik, dan target Lighthouse belum diukur. Pemindaian axe pada homepage desktop 1440 px dan mobile 390 px tidak menemukan pelanggaran pada aturan WCAG A/AA yang diuji; ini tidak menggantikan audit aksesibilitas manual. Hasil verifikasi dicatat di [docs/VALIDATION.md](docs/VALIDATION.md).

## Production

Panduan lengkap ada di [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md). Document root **wajib** menunjuk direktori `public/`. Jangan jalankan PHP development server untuk production. Domain, hosting, HTTPS, database production, SMTP, dan backup diatur pada infrastruktur deployment; belum dipublikasikan dari environment ini.

Spesifikasi acuan: [PRD](docs/PRD.md). CSV import, analytics, role bertingkat, activity log, dan arsip publik merupakan pengembangan P1/P2 dan belum disertakan.
