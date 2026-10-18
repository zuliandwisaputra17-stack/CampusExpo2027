# Deployment Campus Expo

Aplikasi dapat menggunakan shared hosting dengan document root khusus, atau Nginx/Apache + PHP-FPM. Gunakan PHP 8.4+, ekstensi GD dengan WebP, dan `pdo_mysql` untuk MySQL/MariaDB. Environment cloud saat ini memakai SQLite; koneksi MySQL production belum diuji karena host dan kredensial production belum tersedia.

## Konfigurasi

Salin `.env.example` menjadi `.env` di server, lalu atur secara aman:

```dotenv
APP_NAME="Campus Expo"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://expo.sekolah.sch.id
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_expo
DB_USERNAME=campus_expo
# Isi DB_PASSWORD secara aman di server atau environment settings.
MAIL_MAILER=smtp
# Isi MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_SCHEME, dan MAIL_FROM_ADDRESS.
```

Timezone aplikasi ditetapkan ke `Asia/Jakarta` di `config/app.php`. Seluruh waktu event dan jadwal diisi dalam WIB. Jangan menyimpan `.env` atau kredensial ke Git. Gunakan app key yang sama pada semua instance suatu deployment dan simpan aman.

Mailer default development adalah `log`, sehingga tidak mengirim email. Reset kata sandi production membutuhkan SMTP yang sudah berfungsi. Log development dapat berisi tautan reset; jangan membagikan log tersebut. Command `expo:admin` tetap dapat digunakan oleh pengelola server untuk pemulihan akun.

## Build dan migration

Pada server atau pipeline terpercaya:

```bash
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci --no-audit --no-fund
npm run build
# Hanya pada instalasi pertama, bila APP_KEY belum diatur:
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan expo:admin panitia@sekolah.sch.id --name="Panitia"
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jangan menjalankan `db:seed` pada production berisi data sekolah. Isi konten sekolah melalui CMS. Pengguna dapat tetap menelusuri halaman ketika belum ada event aktif; aplikasi menampilkan informasi yang tersedia dan empty state.

`public/build` diabaikan Git, sehingga hasil `npm run build` harus disertakan dalam artefak deployment. Setelah build, Node.js tidak diperlukan oleh PHP saat melayani halaman. Tidak diperlukan queue worker atau cron untuk countdown; status dihitung dari waktu event pada browser.

## Web server dan penyimpanan

Document root harus menunjuk `/path/to/project/public`. Konfigurasi Nginx minimal di dalam server HTTPS:

```nginx
root /path/to/project/public;
index index.php;
client_max_body_size 8m;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ \.php$ {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_pass unix:/run/php/php8.4-fpm.sock;
}
location ~ /\.(?!well-known).* {
    deny all;
}
```

Sesuaikan socket PHP-FPM dengan server. Aktifkan TLS dan redirect HTTP ke HTTPS pada konfigurasi hosting. Pada shared hosting, arahkan domain/subdomain ke `public/`; jangan memindahkan `.env`, `vendor`, `app`, atau database ke direktori publik.

PHP-FPM harus memiliki izin tulis pada `storage/` dan `bootstrap/cache/`. Hindari permission 777. Atur `upload_max_filesize=5M`, `post_max_size=8M`, dan limit reverse proxy sesuai. `storage/app/public` berisi media publik; `storage/app/private` harus tetap privat.

Pada deployment beberapa instance, gunakan database serta storage media bersama; session database sudah dipakai secara default. Konfigurasi public disk harus sesuai bila beralih ke S3; helper media saat ini mengasumsikan URL `/storage/`, sehingga perlu penyesuaian helper sebelum menggunakan disk remote.

## Verifikasi setelah deploy

Periksa HTTPS, halaman `/`, health endpoint `/up`, `/admin/login`, login/logout, unggah foto, edit konten, dan keterlihatan hasil di halaman publik. Pastikan `APP_DEBUG=false`, tidak ada akun development yang dipakai publik, dan foto contoh sudah diganti. Uji pengiriman reset password melalui SMTP.

Jadwalkan backup database harian dan backup media di `storage/app/public` ke lokasi terpisah; uji pemulihan backup. Pertahankan snapshot/database cadangan sebelum migration pada deployment berikutnya. Konfigurasi backup, DNS, TLS, dan pengiriman email mengikuti penyedia hosting dan belum dijalankan dari environment development ini.
