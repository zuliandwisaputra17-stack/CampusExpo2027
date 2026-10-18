@echo off
setlocal
cd /d "%~dp0"
where php >nul 2>nul
if errorlevel 1 (
  echo Buka file ini melalui Terminal Laragon agar PHP tersedia.
  pause
  exit /b 1
)
php -r "exit(PHP_VERSION_ID >= 80200 ? 0 : 1);"
if errorlevel 1 (
  echo Website ini memerlukan PHP 8.2 atau lebih baru. Pilih versi PHP di Laragon.
  pause
  exit /b 1
)
if not exist vendor\autoload.php (
  echo Menyiapkan dependency Laravel. Tunggu sebentar...
  composer install --no-interaction --prefer-dist
  if errorlevel 1 goto failed
)
if not exist .env (
  copy .env.example .env >nul
  php artisan key:generate
  if errorlevel 1 goto failed
)
if not exist database\database.sqlite type nul > database\database.sqlite
if not exist public\build\manifest.json (
  where npm >nul 2>nul
  if errorlevel 1 (
    echo Node.js/npm belum tersedia. Install Node.js LTS dari https://nodejs.org lalu jalankan file ini lagi.
    pause
    exit /b 1
  )
  echo Menyiapkan aset frontend. Tunggu sebentar...
  npm ci --no-audit --no-fund
  if errorlevel 1 goto failed
  npm run build
  if errorlevel 1 goto failed
)
php artisan migrate --force
if errorlevel 1 goto failed
php artisan expo:seed-demo
if errorlevel 1 goto failed
if not exist public\storage mklink /J "%CD%\public\storage" "%CD%\storage\app\public"
echo.
echo Buka alamat ini di Chrome atau Edge: http://127.0.0.1:8000
echo Biarkan jendela ini terbuka selama memakai website.
echo Tekan Ctrl+C untuk menghentikan server.
echo.
php artisan serve --host=127.0.0.1 --port=8000
exit /b
:failed
echo.
echo Ada langkah yang belum berhasil. Kirim pesan error di atas untuk dibantu.
pause
exit /b 1
