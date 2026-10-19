#!/usr/bin/env python3
"""Package a design-only update for an existing Laragon installation."""
import json
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile

root = Path(__file__).resolve().parents[1]
files = [
    'README.md', 'docs/DESIGN-UPDATE.md', 'config/cms.php',
    'package.json', 'package-lock.json', 'vite.config.js', 'tsconfig.json', 'components.json',
    'public/assets/favicon.svg', 'public/build/manifest.json',
    'resources/css/app.css', 'resources/css/base.css', 'resources/css/sky.css',
    'resources/js/app.js', 'resources/js/showcases.tsx',
    'resources/js/lib/utils.ts', 'resources/js/lib/use-motion-state.ts',
    'resources/js/components/ui/tilted-grid-hero.tsx',
    'resources/js/components/ui/tilted-grid-hero.css',
    'resources/js/components/ui/polaroid-line-carousel.tsx',
    'resources/js/components/ui/polaroid-line-carousel.css',
    'resources/views/home.blade.php', 'resources/views/components/icon.blade.php',
    'resources/views/admin/form.blade.php', 'resources/views/admin/layout.blade.php',
    'resources/views/admin/auth-layout.blade.php',
    'tests/Feature/ExpoTest.php', 'tests/browser.mjs', 'tests/accessibility.mjs',
    'scripts/package-design-update.py',
]
manifest = json.loads((root / 'public/build/manifest.json').read_text())
for entry in manifest.values():
    files.append('public/build/' + entry['file'])
    for asset in entry.get('css', []) + entry.get('assets', []):
        files.append('public/build/' + asset)
files = sorted(set(files))
for name in files:
    path = (root / name).resolve()
    if not path.is_relative_to(root) or not path.is_file():
        raise RuntimeError(f'Missing or invalid update file: {name}')

instructions = """CAMPUS EXPO - PEMBARUAN DESAIN BIRU MUDA

Paket ini untuk proyek yang sudah berjalan, bukan instalasi baru.

1. Hentikan php artisan serve dan npm run dev (jika aktif) dengan Ctrl+C.
2. Salin SEMUA isi ZIP ini ke:
   C:\\laragon\\www\\CampusExpo2027-campus-expo-website
   Pilih Replace untuk file versi lama.
3. Di CMD/Terminal Laragon, jalankan:

   cd /d C:\\laragon\\www\\CampusExpo2027-campus-expo-website
   php artisan config:clear
   php artisan view:clear
   php artisan serve

4. Buka http://127.0.0.1:8000 dan tekan Ctrl+F5.

Aset sudah dibangun. Tidak perlu composer install atau npm install untuk
memakai paket ini. Database, akun admin, .env dan foto unggahan tidak diganti.
Tidak perlu migrate:fresh atau seeder.

Jika desain lama masih muncul, tutup Vite dan periksa public/hot.
Hapus hanya public/hot yang tertinggal, lalu muat ulang browser.

Media sosial: Portal Panitia > Anggota > Edit > isi URL Instagram/LinkedIn.
Petunjuk lengkap tersedia di docs/DESIGN-UPDATE.md.
"""
destination = root / 'downloads/CampusExpo2027-Update-Biru.zip'
destination.parent.mkdir(exist_ok=True)
with ZipFile(destination, 'w', ZIP_DEFLATED) as archive:
    for name in files:
        archive.write(root / name, name)
    archive.writestr('CARA-UPDATE-DESAIN.txt', instructions.replace('\n', '\r\n'))
with ZipFile(destination) as archive:
    assert archive.testzip() is None, 'Archive integrity check failed'
    assert set(archive.namelist()) == set(files + ['CARA-UPDATE-DESAIN.txt'])
    assert not any(name.startswith(('.env', 'database/', 'storage/', 'vendor/', 'public/storage/', 'composer.')) for name in archive.namelist())
print(f'{destination.name}: {len(files) + 1} files, {destination.stat().st_size:,} bytes; integrity verified.')
