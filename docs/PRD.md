# PRODUCT REQUIREMENTS DOCUMENT
## Campus Expo SMA — Full-Stack Laravel Website

**Versi:** 1.0  
**Platform:** Web Responsive  
**Framework utama:** Laravel  
**Jenis website:** Public Single Page Website + Admin Dashboard  
**Target deployment:** Shared Hosting / VPS / Cloud Hosting  
**Target pengguna:** Siswa SMA, orang tua, guru, alumni, mahasiswa perwakilan kampus, panitia Campus Expo

---

# 1. PRODUCT OVERVIEW

## 1.1 Nama Produk

**Campus Expo SMA [Nama Sekolah]**

Contoh:

> Campus Expo SMAN 1 [Nama Kota] 2027

Website berfungsi sebagai pusat informasi resmi Campus Expo yang diselenggarakan sekolah setiap tahun.

Website memiliki dua bagian utama:

### Public Website

Public website menggunakan konsep **Single Page Website**, sehingga informasi utama Campus Expo tersedia dalam satu halaman dengan navigasi antar-section menggunakan smooth scrolling.

Public website berisi:

- Hero interaktif dengan visual sekolah berbentuk 3D.
- Countdown menuju Campus Expo.
- Informasi Campus Expo.
- Daftar universitas.
- University Explorer dengan carousel drag/swipe.
- Daftar anggota/perwakilan kampus.
- Informasi jurusan dan program studi.
- Jadwal kegiatan Campus Expo.
- Informasi lokasi.
- Statistik Campus Expo.
- FAQ.
- Partner/sponsor.
- Galeri.
- Kontak dan social media.

### Admin Dashboard

Dashboard hanya dapat diakses oleh administrator/panitia untuk mengelola seluruh isi website tanpa mengubah source code.

URL:

`/admin`

---

# 2. PRODUCT GOAL

Website harus menjadi:

1. Pusat informasi resmi Campus Expo sekolah.
2. Media pengenalan universitas kepada siswa.
3. Direktori anggota/alumni/mahasiswa yang mengikuti Campus Expo.
4. Media publikasi jadwal Campus Expo.
5. Website Campus Expo yang dapat digunakan kembali setiap tahun.
6. Website dengan tampilan modern dan interaktif.
7. Website yang mudah dikelola panitia melalui dashboard.
8. Website yang responsive di desktop, tablet, dan smartphone.
9. Website yang siap di-deploy ke hosting production.

---

# 3. PRODUCT PRINCIPLES

Desain website harus memiliki karakter:

- Modern.
- Akademik.
- Youthful.
- Clean.
- Interactive.
- Premium tetapi tidak berlebihan.
- Mudah digunakan oleh siswa SMA.
- Banyak visual namun tetap cepat.
- Animasi tidak mengganggu akses informasi.

Inspirasi experience:

- Landing page universitas modern.
- Website event teknologi.
- Interaksi seperti Apple product page.
- Card-based design.
- 3D parallax.
- Horizontal drag interaction.
- Smooth scrolling.

Animasi digunakan sebagai enhancement, bukan menggantikan informasi penting.

---

# 4. USER PERSONA

## 4.1 Siswa SMA

Tujuan:

- Mengetahui universitas yang hadir.
- Mengetahui siapa perwakilan masing-masing kampus.
- Mengetahui jurusan/prodi.
- Membuka website universitas.
- Melihat jadwal Campus Expo.
- Mengetahui berapa lama lagi Campus Expo dimulai.

---

## 4.2 Orang Tua

Tujuan:

- Mengetahui detail Campus Expo.
- Mengetahui universitas peserta.
- Mengetahui jadwal kegiatan.
- Mendapatkan informasi lokasi.

---

## 4.3 Anggota / Perwakilan Kampus

Tujuan:

- Mengetahui bahwa profilnya sudah tercantum.
- Membantu siswa mengetahui latar belakang kampus dan prodi anggota.

---

## 4.4 Panitia

Tujuan:

- Menambahkan universitas.
- Menambahkan anggota.
- Mengubah anggota.
- Menghapus anggota.
- Mengatur jadwal.
- Mengubah tanggal Campus Expo.
- Mengubah foto.
- Mengelola informasi tanpa menyentuh source code.

---

# 5. INFORMATION ARCHITECTURE

Public website menggunakan satu halaman:

`/`

Urutan section yang direkomendasikan:

1. Navbar
2. Hero
3. Countdown
4. Tentang Campus Expo
5. Campus Explorer
6. Anggota Campus Expo
7. Jadwal Campus Expo
8. Statistik Campus Expo
9. Informasi Lokasi
10. Galeri
11. Partner / Sponsor
12. FAQ
13. CTA / Contact
14. Footer

Dashboard berada di:

`/admin`

Authentication:

`/admin/login`

---

# 6. NAVIGATION

Navbar desktop:

**Logo | Home | Campus | Anggota | Jadwal | Tentang | FAQ**

CTA:

**Explore Campus**

Navbar bersifat sticky.

Saat pengguna scroll, menu aktif mengikuti section yang sedang terlihat.

Contoh:

`Home → Campus → Anggota → Jadwal → Tentang`

Pada mobile menggunakan hamburger navigation.

Semua link navigasi public menggunakan smooth scroll.

---

# 7. HERO SECTION

Hero merupakan bagian visual utama website.

## 7.1 Hero Content

Contoh:

**Eyebrow**

`CAMPUS EXPO 2027`

**Heading**

> Temukan Kampus.  
> Kenali Pilihanmu.  
> Tentukan Masa Depanmu.

**Description**

> Jelajahi universitas, program studi, dan pengalaman langsung dari mahasiswa dan alumni dalam Campus Expo SMA [Nama Sekolah].

CTA utama:

**Jelajahi Kampus**

CTA kedua:

**Lihat Jadwal**

---

# 8. 3D SCHOOL HERO EXPERIENCE

Hero harus memiliki elemen foto sekolah yang tampil secara 3D dan interaktif.

Contoh konsep:

Foto gedung SMA berada pada sisi kanan hero.

Foto tidak hanya berbentuk gambar biasa tetapi berada dalam sebuah interactive 3D scene.

Teknologi yang direkomendasikan:

**Three.js / WebGL**

Alternatif fallback:

CSS 3D Transform.

---

## 8.1 Desktop Interaction

Saat mouse bergerak:

- Gedung sedikit mengikuti arah cursor.
- Efek perspective.
- Subtle rotation.
- Depth movement.
- Shadow berubah mengikuti posisi.
- Background particle bergerak perlahan.
- Decorative element memiliki parallax berbeda.

Contoh:

Mouse ke kanan:

`schoolImage.rotateY = positive`

Mouse ke atas:

`schoolImage.rotateX = negative`

Gerakan harus lembut.

Tidak boleh membuat user pusing.

---

## 8.2 Scroll Interaction

Saat pengguna mulai scroll:

- Hero image sedikit bergerak ke atas.
- Background bergerak lebih lambat.
- Foreground bergerak sedikit lebih cepat.

Menciptakan efek depth/parallax.

---

## 8.3 Mobile

Tidak menggunakan mouse interaction.

Diganti:

- Floating animation ringan.
- Touch tilt opsional.
- Parallax sederhana.

---

## 8.4 Accessibility

Jika browser memiliki:

`prefers-reduced-motion: reduce`

maka animasi intensif otomatis dinonaktifkan.

Jika WebGL gagal:

tampilkan foto sekolah statis.

Dengan demikian informasi website tetap dapat digunakan.

---

# 9. COUNTDOWN CAMPUS EXPO

Di bawah Hero terdapat countdown.

Contoh:

> Campus Expo dimulai dalam

**32 : 14 : 27 : 42**

Hari | Jam | Menit | Detik

Sistem menggunakan:

`event_start_at`

Contoh:

`2027-01-18 08:00:00`

Timezone:

`Asia/Jakarta`

---

# 10. COUNTDOWN STATES

## STATE 1 — Upcoming

Jika:

`current_time < event_start`

Tampilkan countdown.

---

## STATE 2 — Live

Jika:

`event_start <= current_time <= event_end`

Countdown berubah menjadi:

**CAMPUS EXPO SEDANG BERLANGSUNG**

Badge:

`LIVE`

CTA:

**Lihat Jadwal Hari Ini**

---

## STATE 3 — Finished

Jika:

`current_time > event_end`

Tampilkan:

**Campus Expo 2027 telah selesai.**

CTA dapat berubah menjadi:

**Lihat Dokumentasi**

---

# 11. ABOUT CAMPUS EXPO

Section memberikan penjelasan singkat mengenai acara.

Isi dapat dikelola melalui dashboard.

Contoh statistik:

**25+**
Universitas

**50+**
Mahasiswa & Alumni

**30+**
Program Studi

**500+**
Pengunjung

Angka harus bisa diedit dari admin.

---

# 12. CAMPUS EXPLORER

Salah satu fitur utama website.

Heading:

> Kampus Mana yang Ingin Kamu Kenal?

Subheading:

> Geser untuk menjelajahi universitas yang hadir di Campus Expo.

---

# 13. UNIVERSITY CAROUSEL

Campus ditampilkan dalam card horizontal.

Contoh:

`[ UI ] [ ITB ] [ UGM ] [ UNAIR ] [ UNDIP ] [ IPB ]`

User dapat:

- Drag menggunakan mouse.
- Swipe di mobile.
- Menggunakan scroll wheel.
- Menggunakan tombol next.
- Menggunakan tombol previous.

Library yang direkomendasikan:

**Swiper.js**

atau

**Embla Carousel**

---

# 14. CAMPUS CARD

Setiap card berisi:

- Foto universitas.
- Logo universitas.
- Nama universitas.
- Singkatan.
- Kota.
- Jumlah anggota pada Campus Expo.

Contoh:

> UNIVERSITAS INDONESIA  
> Depok, Jawa Barat  
> 7 anggota

---

# 15. CAMPUS CARD INTERACTION

Saat hover:

- Card sedikit naik.
- Foto zoom sekitar 3–5%.
- Glow / shadow muncul.
- Logo bergerak sedikit.
- Cursor menjadi pointer.

Saat card diklik:

Website melakukan:

`scroll → section anggota`

kemudian otomatis:

`filter anggota berdasarkan campus_id`

Misalnya user memilih:

**Universitas Indonesia**

maka section anggota otomatis menjadi:

> Anggota dari Universitas Indonesia

dan hanya card anggota UI yang ditampilkan.

URL dapat berubah menjadi:

`/?campus=universitas-indonesia#anggota`

Sehingga link tersebut juga bisa dibagikan.

---

# 16. RESET FILTER

Pada section Anggota terdapat:

`Semua Kampus`

Jika ditekan, semua anggota kembali muncul.

Filter tambahan:

- Semua Kampus
- Nama Kampus
- Jurusan
- Program Studi

Search:

`Cari anggota atau program studi...`

---

# 17. MEMBER SECTION

Heading:

> Kenalan dengan Mereka

Deskripsi:

> Temukan mahasiswa dan alumni dari berbagai universitas dan cari tahu jurusan serta program studi yang mereka jalani.

---

# 18. MEMBER CARD

Member ditampilkan menggunakan responsive grid.

Desktop:

`4 card / row`

Tablet:

`2–3 card / row`

Mobile:

`1–2 card / row`

---

# 19. MEMBER CARD CONTENT

Setiap card memiliki:

### Foto anggota

Portrait ratio:

`4:5`

### Nama

Contoh:

**Aulia Rahman**

### Kampus

`Universitas Indonesia`

### Fakultas

Opsional.

Contoh:

`Fakultas Ilmu Komputer`

### Jurusan

Contoh:

`Ilmu Komputer`

### Program Studi

Contoh:

`S1 Ilmu Komputer`

### Tahun Angkatan

Opsional.

Contoh:

`Angkatan 2025`

### Social Media

Opsional:

- Instagram.
- LinkedIn.

---

# 20. CAMPUS LINK DI MEMBER CARD

Nama kampus merupakan link.

Contoh:

**Universitas Indonesia ↗**

Ketika diklik:

buka website resmi kampus pada tab baru.

Contoh destination:

`https://www.ui.ac.id`

Wajib menggunakan:

`target="_blank"`

dan:

`rel="noopener noreferrer"`

URL kampus disimpan pada database agar dapat diubah admin.

---

# 21. EMPTY STATE MEMBER

Jika kampus belum memiliki anggota:

Tampilkan ilustrasi dan pesan:

> Belum ada anggota Campus Expo dari kampus ini.

CTA:

**Lihat Semua Kampus**

---

# 22. EVENT SCHEDULE

Section:

> Rundown Campus Expo

Schedule menggunakan timeline.

Contoh:

### 07:00 – 07:30

**Registrasi Peserta**

Lobby utama.

---

### 07:30 – 08:00

**Opening Ceremony**

Aula SMA.

---

### 08:00 – 09:30

**Campus Exhibition Session 1**

Booth Universitas.

---

### 09:30 – 10:30

**Sharing Alumni**

Aula Utama.

---

### 10:30 – 12:00

**Campus Exhibition Session 2**

Booth Universitas.

---

Jika event berlangsung lebih dari satu hari:

buat tabs:

`Hari 1 | Hari 2 | Hari 3`

---

# 23. SCHEDULE STATUS

Schedule dapat memiliki status otomatis.

Sebelum kegiatan:

`UPCOMING`

Saat berlangsung:

`NOW`

Setelah selesai:

`FINISHED`

Item yang sedang berlangsung diberi visual highlight.

---

# 24. VENUE SECTION

Menampilkan:

- Nama sekolah.
- Alamat acara.
- Tanggal.
- Jam buka.
- Informasi parkir.
- Informasi kontak.
- Map embed.

CTA:

**Buka Google Maps**

---

# 25. GALLERY SECTION

Menampilkan dokumentasi Campus Expo.

Layout:

Masonry/Grid.

Konten:

- Foto Campus Expo sebelumnya.
- Booth universitas.
- Alumni sharing.
- Pengunjung.
- Opening ceremony.

Foto dapat dibuka menggunakan lightbox.

---

# 26. PARTNER SECTION

Opsional.

Menampilkan:

- Sponsor.
- Media Partner.
- Organisasi sekolah.
- Partner universitas.

Logo berjalan menggunakan marquee animation yang sangat halus.

---

# 27. FAQ

Contoh pertanyaan:

### Apakah Campus Expo terbuka untuk umum?

Jawaban dapat dikelola admin.

### Apakah harus melakukan registrasi?

Jawaban dapat dikelola admin.

### Apa saja kampus yang hadir?

Jawaban dapat dikelola admin.

### Apakah acara gratis?

Jawaban dapat dikelola admin.

FAQ menggunakan accordion.

---

# 28. FOOTER

Isi:

Logo Campus Expo.

Quick Links:

- Campus
- Anggota
- Jadwal
- Tentang
- FAQ

Social media:

- Instagram
- TikTok
- YouTube

Contact:

Email / WhatsApp panitia.

Copyright:

`© 2027 Campus Expo SMA [Nama Sekolah].`

---

# 29. ADMIN DASHBOARD

URL:

`/admin`

Dashboard direkomendasikan menggunakan:

**Filament for Laravel**

atau custom Laravel admin dashboard.

Filament direkomendasikan karena memberikan:

- Authentication.
- CRUD.
- Table.
- Form.
- Image upload.
- Search.
- Filter.
- Pagination.
- Validation.
- Role management.

dengan development time lebih cepat.

---

# 30. ADMIN LOGIN

Form:

**Email**

**Password**

Button:

`Masuk`

Fitur:

- Remember me.
- Forgot password.
- Rate limit login.
- Secure session.

Tidak ada fitur register publik.

Admin dibuat melalui:

Seeder / command / Super Admin.

---

# 31. ADMIN DASHBOARD HOME

Dashboard menampilkan:

### Active Event

`Campus Expo 2027`

### Total Campus

`25`

### Total Anggota

`65`

### Total Schedule

`12`

### Total Gallery

`44`

Quick Actions:

`+ Tambah Anggota`

`+ Tambah Kampus`

`+ Tambah Jadwal`

---

# 32. EVENT EDITION MANAGEMENT

Website harus mendukung Campus Expo tahunan.

Contoh:

- Campus Expo 2025
- Campus Expo 2026
- Campus Expo 2027

Table:

`event_editions`

Field utama:

| Field | Type |
|---|---|
| id | bigint |
| name | varchar |
| year | integer |
| slug | varchar |
| event_start_at | datetime |
| event_end_at | datetime |
| venue_name | varchar |
| venue_address | text |
| description | text |
| hero_title | varchar |
| hero_description | text |
| school_image | varchar |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

Hanya satu edition boleh mempunyai:

`is_active = true`

Public website otomatis mengambil event aktif.

---

# 33. CAMPUS MANAGEMENT

Admin menu:

**Campus**

Kemampuan:

- Add.
- Edit.
- Delete.
- Search.
- Sort.
- Enable/disable.

Database:

`campuses`

| Field | Type |
|---|---|
| id | bigint |
| name | varchar |
| slug | varchar unique |
| abbreviation | varchar |
| logo | varchar |
| cover_image | varchar |
| city | varchar |
| province | varchar nullable |
| description | text nullable |
| official_url | varchar |
| display_order | integer |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

---

# 34. MEMBER MANAGEMENT

Admin menu:

**Anggota**

Admin dapat:

- Menambahkan anggota.
- Mengedit anggota.
- Menghapus anggota.
- Upload foto.
- Menghubungkan anggota dengan kampus.
- Memilih tahun Campus Expo.
- Menentukan urutan tampilan.
- Mengaktifkan/nonaktifkan anggota.

Database:

`members`

| Field | Type |
|---|---|
| id | bigint |
| event_edition_id | foreignId |
| campus_id | foreignId |
| name | varchar |
| slug | varchar |
| photo | varchar |
| faculty | varchar nullable |
| major | varchar nullable |
| study_program | varchar |
| cohort_year | integer nullable |
| bio | text nullable |
| instagram_url | varchar nullable |
| linkedin_url | varchar nullable |
| display_order | integer |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

Relationship:

`EventEdition hasMany Members`

`Campus hasMany Members`

`Member belongsTo Campus`

`Member belongsTo EventEdition`

---

# 35. MEMBER IMAGE MANAGEMENT

Foto anggota:

Format input:

- JPG.
- JPEG.
- PNG.
- WebP.

Maksimum original upload:

5 MB.

Server sebaiknya membuat versi:

- Thumbnail.
- Medium.
- WebP.

Crop ratio:

`4:5`

Admin diberikan preview sebelum simpan.

---

# 36. SCHEDULE MANAGEMENT

Database:

`schedules`

| Field | Type |
|---|---|
| id | bigint |
| event_edition_id | foreignId |
| event_date | date |
| start_time | time |
| end_time | time nullable |
| title | varchar |
| description | text nullable |
| location | varchar nullable |
| speaker | varchar nullable |
| display_order | integer |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

Admin dapat drag/reorder jadwal.

---

# 37. GALLERY MANAGEMENT

Database:

`galleries`

| Field | Type |
|---|---|
| id | bigint |
| event_edition_id | foreignId nullable |
| image | varchar |
| caption | varchar nullable |
| alt_text | varchar |
| display_order | integer |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

---

# 38. FAQ MANAGEMENT

Database:

`faqs`

| Field | Type |
|---|---|
| id | bigint |
| event_edition_id | foreignId nullable |
| question | varchar |
| answer | text |
| display_order | integer |
| is_active | boolean |
| created_at | timestamp |
| updated_at | timestamp |

---

# 39. PARTNER MANAGEMENT

Database:

`partners`

Fields:

- id
- event_edition_id
- name
- logo
- url
- type
- display_order
- is_active

Type:

- Sponsor
- Media Partner
- Community Partner
- School Partner

---

# 40. WEBSITE SETTINGS

Table:

`site_settings`

Contoh data:

- School name.
- School logo.
- School image.
- Contact email.
- WhatsApp.
- Instagram.
- TikTok.
- YouTube.
- Google Maps URL.
- SEO title.
- SEO description.
- Favicon.

---

# 41. ADMIN ROLES

### Super Admin

Akses penuh.

### Content Admin

Dapat mengelola:

- Campus.
- Member.
- Schedule.
- Gallery.
- FAQ.

Tidak dapat:

- Menghapus Super Admin.
- Mengubah konfigurasi sistem penting.

### Viewer

Opsional.

Hanya melihat data.

---

# 42. ACTIVITY LOG

Direkomendasikan mencatat aktivitas admin.

Contoh:

> Admin A menambahkan anggota baru.

> Admin B mengubah jadwal Campus Expo.

> Super Admin menghapus Campus X.

Data:

- user_id
- action
- model
- model_id
- description
- created_at

---

# 43. FRONTEND TECHNOLOGY

Recommended stack:

### Backend

Laravel stable release.

### Database

MySQL / MariaDB.

### Server Rendering

Laravel Blade.

### CSS

Tailwind CSS.

### JavaScript

Alpine.js.

### Bundler

Vite.

### 3D

Three.js.

### Carousel

Swiper.js.

### Scroll Animation

CSS / GSAP bila diperlukan.

### Admin

Filament.

### Icons

Lucide Icons / Heroicons.

---

# 44. WHY BLADE + ALPINE

Tidak diperlukan SPA framework berat seperti React hanya untuk website informasi ini.

Blade memberikan:

- SEO lebih baik.
- Rendering cepat.
- Development Laravel lebih sederhana.
- Deployment lebih mudah.
- Shared hosting friendly.

Alpine.js digunakan untuk:

- Filter.
- Search.
- Dropdown.
- Mobile navigation.
- Modal.
- Accordion.

Three.js hanya digunakan di bagian yang membutuhkan 3D.

---

# 45. PUBLIC ROUTES

```text
GET /
```

Semua konten public berada di halaman tersebut.

Anchor:

```text
/#home
/#about
/#campus
/#anggota
/#jadwal
/#gallery
/#faq
```

Filter campus:

```text
/?campus=ui#anggota
```

---

# 46. ADMIN ROUTES

```text
/admin
/admin/login

/admin/event-editions
/admin/campuses
/admin/members
/admin/schedules
/admin/galleries
/admin/faqs
/admin/partners
/admin/settings
/admin/users
```

Admin route menggunakan middleware:

```text
auth
verified
role
```

---

# 47. MAIN USER FLOW

## Flow A — Mencari Kampus

```text
Landing Page
↓
Hero
↓
Klik "Jelajahi Kampus"
↓
Campus Explorer
↓
Drag carousel
↓
Pilih Universitas
↓
Scroll otomatis ke Anggota
↓
Filter anggota kampus aktif
↓
Melihat anggota
```

---

# 48. USER FLOW — MEMBUKA WEBSITE KAMPUS

```text
Anggota
↓
Klik nama kampus
↓
Open new tab
↓
Website resmi universitas
```

---

# 49. USER FLOW — JADWAL

```text
Landing Page
↓
Countdown
↓
Klik "Lihat Jadwal"
↓
Smooth scroll
↓
Timeline kegiatan
```

---

# 50. USER FLOW ADMIN

```text
/admin/login
↓
Dashboard
↓
Members
↓
Add Member
↓
Upload foto
↓
Pilih Campus
↓
Masukkan Fakultas/Jurusan/Prodi
↓
Save
↓
Data langsung muncul di public website
```

---

# 51. SINGLE PAGE BEHAVIOUR

Public website tidak melakukan page reload saat berpindah section.

Gunakan:

`scrollIntoView()`

dengan:

`behavior: smooth`

Navbar harus memperbarui active menu berdasarkan section yang terlihat menggunakan:

`IntersectionObserver`

---

# 52. CAMPUS FILTER LOGIC

Contoh state:

```text
selectedCampus = null
```

Klik UI:

```text
selectedCampus = "universitas-indonesia"
```

Member list melakukan:

```text
member.campus.slug === selectedCampus
```

Jika:

```text
selectedCampus = null
```

tampilkan seluruh anggota.

Untuk jumlah anggota kecil-menengah, filtering dapat dilakukan di frontend sehingga respons terasa instan.

---

# 53. SEARCH MEMBER

Search harus dapat mencari:

- Nama.
- Kampus.
- Fakultas.
- Jurusan.
- Program Studi.

Contoh:

User mengetik:

`informatika`

Hasil:

- Teknik Informatika.
- Informatika.
- Sistem Informasi jika metadata relevan.

---

# 54. SORTING MEMBER

Default:

`display_order ASC`

Fallback:

`name ASC`

Admin dapat mengubah display order.

---

# 55. RESPONSIVE DESIGN

Breakpoints mengikuti Tailwind.

### Mobile

`< 640px`

### Tablet

`640–1024px`

### Desktop

`> 1024px`

3D effect harus disederhanakan pada perangkat mobile.

---

# 56. UI DESIGN SYSTEM

## Typography

Direkomendasikan:

Heading:

**Plus Jakarta Sans / Sora**

Body:

**Inter**

Alternatif dapat menggunakan satu font untuk menjaga loading tetap ringan.

---

## Border Radius

Card:

`20–24px`

Button:

`12–16px`

---

## Layout

Maximum width:

`1280–1440px`

Content padding desktop:

`24–48px`

Mobile:

`16–20px`

---

# 57. BUTTON STYLE

Primary:

**Explore Campus →**

Secondary:

**Lihat Jadwal**

Hover:

- Slight translate.
- Shadow.
- Icon movement.

Transition sekitar:

`200–300ms`

---

# 58. MICRO INTERACTIONS

Digunakan pada:

- Navbar.
- Buttons.
- Campus card.
- Member card.
- Schedule.
- FAQ.
- Gallery.

Animasi harus subtle.

Hindari terlalu banyak bounce animation.

---

# 59. MEMBER CARD ANIMATION

Saat card masuk viewport:

```text
opacity 0 → 1
translateY 20px → 0
```

Gunakan stagger sekitar:

`50–100ms`

---

# 60. CAMPUS CAROUSEL EXPERIENCE

Desktop:

cursor:

`grab`

Saat drag:

`grabbing`

Tambahkan teks kecil:

**Drag to explore →**

Mobile:

**Swipe to explore →**

---

# 61. SEO

Website harus memiliki:

- Dynamic `<title>`.
- Meta description.
- Canonical URL.
- OpenGraph.
- Twitter Card.
- Favicon.
- Sitemap.
- robots.txt.

Contoh:

**Title**

> Campus Expo 2027 | SMA [Nama Sekolah]

Description:

> Temukan berbagai universitas, mahasiswa, jurusan, program studi dan jadwal Campus Expo SMA [Nama Sekolah].

---

# 62. EVENT STRUCTURED DATA

Implementasikan schema:

`Event`

melalui JSON-LD.

Data:

- Name.
- Start date.
- End date.
- Location.
- Organizer.
- Description.

Membantu mesin pencari memahami acara.

---

# 63. IMAGE SEO

Semua gambar harus memiliki:

`alt`

Contoh:

> Aulia Rahman - Universitas Indonesia

atau:

> Gedung SMA [Nama Sekolah]

---

# 64. PERFORMANCE TARGET

Target Google Lighthouse production:

Performance:

`≥ 85`

Accessibility:

`≥ 90`

Best Practices:

`≥ 90`

SEO:

`≥ 90`

Target LCP:

`< 2.5–3 detik`

dalam kondisi jaringan mobile normal.

---

# 65. IMAGE OPTIMIZATION

Foto harus:

- Resize sebelum ditampilkan.
- Lazy load.
- WebP/AVIF jika memungkinkan.
- Tidak menggunakan foto original 5–10 MB langsung pada public page.

Member thumbnail sekitar:

`400 × 500`

Campus cover sekitar:

`900 × 600`

Hero school image harus dioptimalkan khusus.

---

# 66. 3D PERFORMANCE

Three.js hanya di-load saat hero membutuhkan.

Batasi:

- Polygon.
- Texture size.
- Particle count.
- Shadow computation.

Target:

60 FPS pada desktop modern.

Pada smartphone low-end:

animasi dapat otomatis dikurangi.

---

# 67. ACCESSIBILITY

Minimum requirement:

- Keyboard navigation.
- Visible focus states.
- Semantic HTML.
- ARIA label jika diperlukan.
- Proper heading structure.
- Alt image.
- Sufficient color contrast.
- Reduced motion support.

Carousel juga harus dapat digunakan menggunakan tombol keyboard.

---

# 68. SECURITY

Laravel security requirement:

- CSRF protection.
- XSS escaping.
- SQL injection prevention melalui Eloquent.
- Password hashing.
- Secure session.
- Authentication middleware.
- Authorization.
- Form validation.
- File type validation.
- File size validation.
- Rate limiting.

Production:

```text
APP_ENV=production
APP_DEBUG=false
```

---

# 69. FILE UPLOAD SECURITY

Jangan mempercayai extension file saja.

Validasi:

- MIME type.
- Image dimensions.
- Maximum filesize.
- Random filename.

File tidak boleh menggunakan nama input user langsung sebagai nama final.

---

# 70. ADMIN SECURITY

Login diberikan rate limit.

Contoh:

5 percobaan / menit / IP.

Session menggunakan:

- HTTP only.
- Secure cookie saat HTTPS.
- SameSite configuration.

HTTPS wajib untuk production.

---

# 71. DATABASE INDEX

Tambahkan index ke:

```text
members.event_edition_id
members.campus_id
members.is_active

campuses.slug
campuses.is_active

schedules.event_edition_id
schedules.event_date
```

Untuk menjaga query tetap cepat.

---

# 72. SOFT DELETE

Direkomendasikan untuk:

- Members.
- Campuses.

Menggunakan Laravel:

`SoftDeletes`

Sehingga data yang tidak sengaja dihapus masih dapat dipulihkan.

---

# 73. DATABASE RELATIONSHIP

```text
EventEdition
 ├── hasMany Members
 ├── hasMany Schedules
 ├── hasMany Galleries
 ├── hasMany Partners
 └── hasMany FAQs

Campus
 └── hasMany Members

Member
 ├── belongsTo EventEdition
 └── belongsTo Campus
```

---

# 74. OPTIONAL CSV IMPORT

Untuk event dengan banyak anggota, admin dapat diberikan fitur:

**Import Member CSV**

Template:

```text
name
campus
faculty
major
study_program
cohort_year
```

Sistem memberikan preview sebelum import.

Feature ini dikategorikan **P1**, bukan requirement wajib MVP.

---

# 75. CURRENT EVENT SYSTEM

Admin dapat menentukan:

**Set as Active Event**

Misalnya:

```text
Campus Expo 2026 = inactive
Campus Expo 2027 = active
```

Public website otomatis menampilkan 2027.

Tidak diperlukan perubahan source code setiap tahun.

---

# 76. EVENT ARCHIVE

P1 Feature:

User dapat mengakses event sebelumnya.

Contoh:

`/archive/2026`

Namun karena requirement utama adalah single-page public website, fitur archive tidak menjadi bagian MVP.

---

# 77. ANALYTICS

Direkomendasikan integrasi analytics.

Track:

- Website visitors.
- Campus card click.
- Campus official website click.
- Member search.
- Schedule views.
- CTA click.

Event contoh:

```text
campus_selected
member_card_viewed
official_campus_clicked
schedule_clicked
```

Ini dapat membantu panitia mengetahui kampus yang paling diminati.

---

# 78. ERROR STATES

404:

Tampilkan halaman yang tetap menggunakan branding Campus Expo.

500:

Tampilkan pesan sederhana:

> Terjadi kendala saat memuat halaman. Silakan coba kembali.

Jangan menampilkan stack trace pada production.

---

# 79. LOADING EXPERIENCE

Gunakan skeleton loading pada:

- Member card.
- Gallery.
- Campus card jika data asynchronous.

Hero jangan menggunakan full-screen loading animation yang lama.

---

# 80. EMPTY STATES

Campus kosong:

> Belum ada universitas yang ditambahkan.

Member kosong:

> Belum ada anggota Campus Expo.

Schedule kosong:

> Jadwal Campus Expo akan segera diumumkan.

Gallery kosong:

Section dapat disembunyikan otomatis.

---

# 81. P0 — MVP FEATURES

Fitur wajib sebelum website dianggap selesai:

- Public single page.
- Responsive design.
- Hero.
- School 3D interaction.
- Countdown.
- Campus Explorer.
- Drag/swipe carousel.
- Campus filtering.
- Member cards.
- Official campus URL.
- Schedule.
- About.
- Location.
- FAQ.
- Admin login.
- Campus CRUD.
- Member CRUD.
- Schedule CRUD.
- Event edition management.
- Image upload.
- Website settings.
- SEO.
- Production deployment.

---

# 82. P1 — ENHANCEMENT

Setelah MVP:

- Gallery.
- Sponsor.
- CSV import.
- Analytics.
- Activity logs.
- Admin roles.
- Archive tahun sebelumnya.
- Member social media.
- Advanced animations.
- Schedule live status.

---

# 83. P2 — FUTURE FEATURE

Jika website dikembangkan lebih jauh:

- Registrasi pengunjung.
- QR Code check-in.
- Student account.
- Bookmark campus.
- Favorite campus.
- Campus recommendation quiz.
- Live attendance.
- Booth QR code.
- Feedback form.
- E-certificate.
- Notification.
- Campus comparison.

---

# 84. DIRECTORY STRUCTURE

Contoh struktur Laravel:

```text
app/
├── Models/
│   ├── Campus.php
│   ├── Member.php
│   ├── EventEdition.php
│   ├── Schedule.php
│   ├── Gallery.php
│   ├── Faq.php
│   └── Partner.php
│
├── Http/
│   └── Controllers/
│       └── HomeController.php
│
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   │
│   ├── sections/
│   │   ├── navbar.blade.php
│   │   ├── hero.blade.php
│   │   ├── countdown.blade.php
│   │   ├── about.blade.php
│   │   ├── campuses.blade.php
│   │   ├── members.blade.php
│   │   ├── schedule.blade.php
│   │   ├── gallery.blade.php
│   │   ├── faq.blade.php
│   │   └── footer.blade.php
│   │
│   └── home.blade.php
│
├── js/
│   ├── app.js
│   ├── hero-3d.js
│   ├── campus-carousel.js
│   └── countdown.js
│
└── css/
    └── app.css
```

---

# 85. HOME CONTROLLER

Public controller bertanggung jawab mengambil:

```text
Active Event
Campuses
Members
Schedules
Gallery
FAQ
Partners
Site Settings
```

Hanya data:

`is_active = true`

yang ditampilkan.

---

# 86. QUERY OPTIMIZATION

Laravel menggunakan eager loading.

Contoh:

```php
Member::with('campus')
```

bukan menjalankan campus query untuk setiap member.

Gunakan caching untuk:

- Site settings.
- Active event.
- Campus list.

---

# 87. CACHE

Contoh cache:

```text
site_settings
active_event
active_campuses
```

Cache invalidated ketika admin melakukan perubahan.

---

# 88. STORAGE

Development:

`storage/app/public`

Production:

Laravel public storage.

Run:

```text
php artisan storage:link
```

Jika menggunakan cloud:

dapat diganti menjadi S3-compatible storage tanpa mengubah struktur aplikasi secara besar.

---

# 89. ENVIRONMENT

Contoh `.env` production:

```text
APP_NAME="Campus Expo"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://campusexpo.sekolah.sch.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=campus_expo
DB_USERNAME=...
DB_PASSWORD=...

APP_TIMEZONE=Asia/Jakarta
```

Secret credential tidak boleh disimpan dalam repository Git.

---

# 90. DEPLOYMENT REQUIREMENTS

Hosting harus mendukung:

- PHP yang kompatibel dengan Laravel stable.
- Composer.
- MySQL/MariaDB.
- PHP extensions Laravel.
- HTTPS/SSL.
- Rewrite rules.
- Cron jika scheduler digunakan.

---

# 91. PRODUCTION BUILD

Sebelum deployment:

```text
composer install --no-dev --optimize-autoloader

npm install

npm run build
```

Laravel:

```text
php artisan migrate --force

php artisan storage:link

php artisan config:cache

php artisan route:cache

php artisan view:cache
```

---

# 92. SHARED HOSTING DEPLOYMENT

Jika menggunakan cPanel/shared hosting:

Idealnya document root diarahkan ke:

```text
/project/public
```

Jika hosting memungkinkan custom document root.

Jangan mengekspos:

```text
.env
vendor
storage
app
database
```

ke public internet.

---

# 93. VPS DEPLOYMENT

Stack yang disarankan:

```text
Nginx
PHP-FPM
MySQL
Laravel
SSL
```

Tambahan opsional:

```text
Redis
Supervisor
Queue Worker
```

---

# 94. DOMAIN

Contoh:

`campusexpo.namasekolah.sch.id`

atau:

`expo.namasekolah.sch.id`

atau domain tersendiri:

`campusexpo2027.id`

Subdomain sekolah lebih direkomendasikan untuk branding resmi.

---

# 95. SSL

Production wajib menggunakan:

`HTTPS`

HTTP harus redirect ke HTTPS.

---

# 96. BACKUP

Minimal backup:

### Database

Daily.

### Uploaded Images

Daily / Weekly.

Backup disimpan di luar server utama bila memungkinkan.

---

# 97. TESTING

## Unit Test

Test:

- Event active logic.
- Countdown state.
- Campus relationship.
- Member relationship.

---

## Feature Test

Test:

- Homepage accessible.
- Admin login.
- Campus CRUD.
- Member CRUD.
- Schedule CRUD.
- Unauthorized admin blocked.

---

## Browser Test

Test:

- Chrome.
- Firefox.
- Edge.
- Safari.
- Android Chrome.
- iOS Safari.

---

# 98. MOBILE TEST

Harus dites minimal pada:

`360px`

`390px`

`430px`

Pastikan:

- Tidak horizontal overflow.
- Carousel dapat swipe.
- Navbar berfungsi.
- Member card tidak terlalu kecil.
- 3D hero tidak menyebabkan lag.

---

# 99. ACCEPTANCE CRITERIA — HERO

Hero dianggap selesai jika:

- Foto sekolah tampil.
- Desktop memiliki interaksi 3D/parallax.
- Mobile tetap stabil.
- Ada fallback tanpa WebGL.
- Tidak menghambat pengguna membaca heading.
- Reduced motion didukung.

---

# 100. ACCEPTANCE CRITERIA — COUNTDOWN

Countdown dianggap selesai jika:

- Hari/jam/menit/detik tepat.
- Mengikuti timezone Asia/Jakarta.
- Berubah ke LIVE saat event dimulai.
- Berubah ke finished setelah acara selesai.
- Refresh halaman tidak merusak waktu.

---

# 101. ACCEPTANCE CRITERIA — CAMPUS EXPLORER

Feature selesai jika:

- Campus muncul sebagai card.
- Bisa drag desktop.
- Bisa swipe mobile.
- Bisa menggunakan tombol navigasi.
- Clicking campus memilih filter.
- Halaman smooth scroll ke Anggota.
- Campus aktif terlihat jelas.

---

# 102. ACCEPTANCE CRITERIA — MEMBER

Feature selesai jika:

- Foto anggota muncul.
- Nama muncul.
- Kampus muncul.
- Jurusan muncul.
- Program studi muncul.
- Data sesuai tahun event.
- Filter campus bekerja.
- Search bekerja.
- Klik nama kampus membuka official website.
- Link dibuka pada tab baru.

---

# 103. ACCEPTANCE CRITERIA — ADMIN

Admin selesai jika panitia dapat:

1. Login.
2. Membuat Campus Expo tahun baru.
3. Menetapkan event aktif.
4. Menambah kampus.
5. Upload foto kampus.
6. Menambah anggota.
7. Upload foto anggota.
8. Mengubah anggota.
9. Menghapus/nonaktifkan anggota.
10. Menambah jadwal.
11. Mengubah informasi website.
12. Melihat perubahan pada public website.

Semua tanpa mengedit source code.

---

# 104. ACCEPTANCE CRITERIA — DEPLOYMENT

Website dianggap production-ready apabila:

- Domain aktif.
- HTTPS aktif.
- Database migration berhasil.
- Storage upload bekerja.
- Admin login bekerja.
- Public website dapat diakses.
- APP_DEBUG false.
- Tidak terdapat console error kritis.
- Mobile responsive.
- Database memiliki backup.
- Email/error logging dikonfigurasi jika digunakan.

---

# 105. RECOMMENDED HOMEPAGE EXPERIENCE

Urutan final:

```text
NAVBAR
        ↓
3D HERO SCHOOL
        ↓
COUNTDOWN
        ↓
ABOUT + STATISTICS
        ↓
UNIVERSITY EXPLORER
← DRAG / SWIPE →
        ↓
MEMBER DIRECTORY
        ↓
EVENT SCHEDULE
        ↓
VENUE
        ↓
GALLERY
        ↓
PARTNER
        ↓
FAQ
        ↓
CTA
        ↓
FOOTER
```

---

# 106. IDEAL VISUAL EXPERIENCE

Ketika user pertama masuk:

Gedung sekolah tampil sebagai objek visual besar di Hero.

Cursor bergerak:

gedung memberikan efek depth.

User scroll:

Countdown masuk dengan subtle animation.

Kemudian muncul:

> Kampus mana yang ingin kamu kenal?

Di bawahnya user melihat kumpulan card universitas yang dapat:

**drag → drag → drag**

User melihat:

`UI`

dan menekannya.

Card UI menjadi aktif.

Website kemudian melakukan smooth scroll.

Heading berubah menjadi:

> Anggota dari Universitas Indonesia

Semua anggota UI muncul.

User melihat:

**Aulia Rahman**

`Universitas Indonesia ↗`

`Fakultas Ilmu Komputer`

`S1 Ilmu Komputer`

User mengklik:

**Universitas Indonesia ↗**

website resmi UI terbuka pada tab baru.

User kembali ke Campus Expo.

Scroll sedikit ke bawah.

Muncul:

> Rundown Campus Expo

Dengan kegiatan yang sedang berlangsung diberi badge:

**NOW**

Keseluruhan experience terasa seperti website event modern, bukan sekadar website sekolah statis.

---

# 107. RECOMMENDED MVP TECH STACK

Final recommendation:

```text
Laravel
+
Blade
+
Tailwind CSS
+
Alpine.js
+
Three.js
+
Swiper.js
+
Filament Admin
+
MySQL
+
Vite
```

Arsitektur ini dipilih karena:

- Cocok untuk Laravel.
- SEO friendly.
- Cepat.
- Tidak terlalu kompleks.
- Mudah di-hosting.
- Admin mudah dibuat.
- Bisa digunakan kembali setiap tahun.
- Mendukung animasi/interaksi modern.
- Tidak membutuhkan React/Vue untuk seluruh website.

---

# 108. DEVELOPMENT PHASE

## Phase 1 — Foundation

- Setup Laravel.
- Database.
- Authentication.
- Admin dashboard.
- Event edition.

## Phase 2 — CMS

- Campus CRUD.
- Member CRUD.
- Schedule CRUD.
- Settings.

## Phase 3 — Public Website

- Navbar.
- Hero.
- Countdown.
- About.
- Campus Explorer.
- Member directory.
- Schedule.

## Phase 4 — Experience

- Three.js hero.
- Carousel.
- Animation.
- Filters.
- Search.

## Phase 5 — Content

- Gallery.
- FAQ.
- Partner.
- Location.

## Phase 6 — Optimization

- SEO.
- Image optimization.
- Accessibility.
- Performance.
- Security.

## Phase 7 — Production

- Hosting.
- Domain.
- SSL.
- Database.
- Backup.
- Final testing.

---

# 109. DEFINITION OF DONE

Project dianggap selesai jika seorang panitia non-programmer dapat masuk ke `/admin`, memilih Campus Expo tahun aktif, memasukkan daftar universitas, anggota, foto, jurusan, program studi, website resmi universitas, serta jadwal acara; seluruh informasi tersebut kemudian tampil otomatis pada website public single-page tanpa perlu mengubah source code.

Pengunjung dapat menjelajahi universitas menggunakan carousel drag/swipe, memilih salah satu universitas, otomatis diarahkan ke section anggota dengan filter kampus terkait, melihat profil para anggota, membuka website resmi universitas, melihat countdown serta jadwal Campus Expo, dan menikmati Hero sekolah dengan pengalaman visual 3D yang tetap responsif dan nyaman pada desktop maupun perangkat mobile.

Website harus dapat digunakan kembali untuk Campus Expo tahun berikutnya hanya dengan membuat **Event Edition baru** melalui dashboard.

---

# 110. FINAL PRODUCT SUMMARY

Produk akhir terdiri dari:

**PUBLIC**

`https://domaincampusexpo.com/`

Single-page interactive Campus Expo website.

**ADMIN**

`https://domaincampusexpo.com/admin`

Laravel CMS dashboard.

**DATABASE**

MySQL/MariaDB.

**3D EXPERIENCE**

Three.js interactive school hero.

**CAMPUS EXPERIENCE**

Drag/swipe Campus Explorer.

**MEMBER EXPERIENCE**

Campus-filtered member directory.

**EVENT EXPERIENCE**

Real-time countdown + schedule timeline.

**CMS**

Campus, member, schedule, event, gallery, FAQ, partner, dan website settings.

**DEPLOYMENT**

Production-ready Laravel application dengan HTTPS, optimized build, database migration, image storage, caching, security, dan backup.

Dengan struktur ini, website Campus Expo tidak hanya berfungsi sebagai website informasi, tetapi juga menjadi **platform Campus Expo tahunan yang bisa terus dipakai, diedit, dan dikembangkan tanpa harus membuat website dari nol setiap tahun.**