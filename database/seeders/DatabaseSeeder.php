<?php

namespace Database\Seeders;

use App\Models\Campus;
use App\Models\EventEdition;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\Partner;
use App\Models\Schedule;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $event = EventEdition::firstOrCreate(['slug' => 'campus-expo-2027'], ['name' => 'Campus Expo 2027', 'year' => 2027, 'event_start_at' => '2027-01-18 08:00:00', 'event_end_at' => '2027-01-18 15:00:00', 'venue_name' => 'SMA Nusantara', 'venue_address' => 'Jakarta, Indonesia — alamat lengkap akan diumumkan panitia.', 'description' => 'Satu hari untuk membuka banyak kemungkinan. Campus Expo mempertemukan kamu dengan universitas, mahasiswa, dan alumni untuk berbagi cerita tentang kehidupan perkuliahan. Temukan bidang yang kamu suka, tanyakan hal yang membuatmu penasaran, dan mulai rencanakan langkah berikutnya.', 'hero_title' => 'Masa depanmu, dimulai dari sini.', 'hero_description' => 'Kenali kampus impian, temukan jurusan yang kamu suka, dan dengarkan cerita langsung dari mereka yang sudah melangkah.', 'school_image' => 'assets/school.webp', 'is_active' => true]);
        $campusData = [['Universitas Indonesia', 'UI', 'universitas-indonesia', 'Depok', 'Jawa Barat', 'https://www.ui.ac.id'], ['Institut Teknologi Bandung', 'ITB', 'institut-teknologi-bandung', 'Bandung', 'Jawa Barat', 'https://www.itb.ac.id'], ['Universitas Gadjah Mada', 'UGM', 'universitas-gadjah-mada', 'Yogyakarta', 'DI Yogyakarta', 'https://ugm.ac.id'], ['IPB University', 'IPB', 'ipb-university', 'Bogor', 'Jawa Barat', 'https://www.ipb.ac.id'], ['Universitas Airlangga', 'UNAIR', 'universitas-airlangga', 'Surabaya', 'Jawa Timur', 'https://unair.ac.id'], ['Universitas Diponegoro', 'UNDIP', 'universitas-diponegoro', 'Semarang', 'Jawa Tengah', 'https://undip.ac.id']];
        $campuses = [];
        foreach ($campusData as $i => $c) {
            $campuses[] = Campus::firstOrCreate(['slug' => $c[2]], ['name' => $c[0], 'abbreviation' => $c[1], 'city' => $c[3], 'province' => $c[4], 'official_url' => $c[5], 'cover_image' => 'assets/campus-'.($i % 4 + 1).'.webp', 'display_order' => $i, 'is_active' => true]);
        }
        $people = [['Aulia Rahma', 0, 'Ilmu Komputer', 'S1 Ilmu Komputer', 'Fakultas Ilmu Komputer'], ['Rizky Pratama', 1, 'Teknik Informatika', 'S1 Teknik Informatika', 'Sekolah Teknik Elektro dan Informatika'], ['Nadia Putri', 2, 'Psikologi', 'S1 Psikologi', 'Fakultas Psikologi'], ['Fajar Aditya', 3, 'Agribisnis', 'S1 Agribisnis', 'Fakultas Ekonomi dan Manajemen'], ['Dinda Maharani', 0, 'Kedokteran', 'S1 Pendidikan Dokter', 'Fakultas Kedokteran'], ['Arif Ramadhan', 4, 'Manajemen', 'S1 Manajemen', 'Fakultas Ekonomi dan Bisnis'], ['Salsabila Zahra', 5, 'Arsitektur', 'S1 Arsitektur', 'Fakultas Teknik'], ['Kevin Wijaya', 1, 'Teknik Sipil', 'S1 Teknik Sipil', 'Fakultas Teknik Sipil dan Lingkungan']];
        foreach ($people as $i => $person) {
            Member::firstOrCreate(['slug' => Str::slug($person[0]), 'event_edition_id' => $event->id], ['name' => $person[0], 'campus_id' => $campuses[$person[1]]->id, 'major' => $person[2], 'study_program' => $person[3], 'faculty' => $person[4], 'cohort_year' => 2025, 'photo' => 'assets/member-'.($i % 4 + 1).'.webp', 'bio' => 'Siap berbagi cerita tentang kampus, jurusan, dan perjalanan masuk kuliah.', 'display_order' => $i, 'is_active' => true]);
        }
        foreach ([['08:00', '08:30', 'Registrasi & check-in', 'Lobby utama', 'Ambil panduan acara dan kenali area Campus Expo.'], ['08:30', '09:00', 'Opening ceremony', 'Aula Nusantara', 'Pembukaan Campus Expo bersama keluarga besar sekolah.'], ['09:00', '11:00', 'Jelajahi kampus impianmu', 'Area booth kampus', 'Ngobrol langsung dengan mahasiswa dan perwakilan kampus.'], ['11:00', '12:00', 'Cerita dari bangku kuliah', 'Aula Nusantara', 'Sharing alumni tentang jurusan, seleksi masuk, dan kehidupan kampus.'], ['12:00', '13:00', 'Istirahat & networking', 'Area istirahat', 'Waktunya mengisi energi dan berkenalan dengan teman baru.'], ['13:00', '15:00', 'Campus exhibition & closing', 'Area booth kampus', 'Lanjutkan eksplorasi dan bawa pulang rencana masa depanmu.']] as $i => $s) {
            Schedule::firstOrCreate(['event_edition_id' => $event->id, 'title' => $s[2]], ['event_date' => '2027-01-18', 'start_time' => $s[0], 'end_time' => $s[1], 'location' => $s[3], 'description' => $s[4], 'display_order' => $i, 'is_active' => true]);
        }
        foreach ([['Apakah Campus Expo terbuka untuk umum?', 'Acara ini ditujukan untuk siswa SMA, orang tua, guru, dan alumni. Informasi akses pengunjung akan diumumkan panitia melalui website ini.'], ['Apakah ada biaya untuk mengikuti acara?', 'Campus Expo tidak dipungut biaya. Kamu bisa mengenal berbagai kampus dan berbincang langsung dengan perwakilannya.'], ['Apakah harus melakukan registrasi?', 'Kamu dapat melakukan registrasi langsung di lokasi. Silakan datang lebih awal agar punya lebih banyak waktu menjelajahi booth kampus.'], ['Apa yang perlu saya bawa?', 'Bawa kartu pelajar, alat tulis, dan daftar pertanyaanmu. Jangan lupa semangat untuk mencari tahu tentang masa depan!'], ['Kampus apa saja yang akan hadir?', 'Lihat daftar peserta pada bagian Jelajahi Kampus. Daftar akan diperbarui oleh panitia menjelang pelaksanaan acara.']] as $i => $f) {
            Faq::firstOrCreate(['question' => $f[0]], ['answer' => $f[1], 'event_edition_id' => $event->id, 'display_order' => $i, 'is_active' => true]);
        }
        foreach (['Cerita baru, koneksi baru.', 'Merayakan langkah berikutnya.', 'Belajar langsung dari pengalaman.'] as $i => $caption) {
            Gallery::firstOrCreate(['image' => 'assets/gallery-'.($i + 1).'.webp'], ['caption' => $caption, 'alt_text' => 'Ilustrasi kegiatan dan kehidupan mahasiswa', 'event_edition_id' => $event->id, 'display_order' => $i, 'is_active' => true]);
        }
        foreach (['OSIS Nusantara', 'Ikatan Alumni', 'Media Pelajar', 'Ruang Edukasi'] as $i => $name) {
            Partner::firstOrCreate(['name' => $name], ['type' => 'School Partner', 'display_order' => $i, 'is_active' => true]);
        }
        foreach (['school_name' => 'SMA Nusantara', 'contact_email' => '', 'whatsapp' => '', 'instagram' => '', 'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Jakarta+Indonesia', 'seo_title' => 'Campus Expo 2027 — Temukan Arah Masa Depanmu', 'seo_description' => 'Jelajahi universitas, kenali program studi, dan berbagi cerita bersama mahasiswa dan alumni di Campus Expo 2027.', 'stat_campuses' => '6', 'stat_members' => '8', 'stat_programs' => '8', 'stat_visitors' => '500+', 'parking_info' => 'Informasi parkir dan alamat lengkap akan diumumkan panitia.'] as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
