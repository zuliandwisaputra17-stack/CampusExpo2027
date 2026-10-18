<?php

use App\Models\Campus;
use App\Models\EventEdition;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\Partner;
use App\Models\Schedule;

return [
    'event-editions' => ['label' => 'Edisi Acara', 'model' => EventEdition::class, 'fields' => [
        'name' => ['label' => 'Nama acara', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'year' => ['label' => 'Tahun', 'type' => 'number', 'rules' => 'required|integer|min:2020|max:2100'],
        'slug' => ['label' => 'Slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:255'],
        'event_start_at' => ['label' => 'Mulai (WIB)', 'type' => 'datetime-local', 'rules' => 'required|date'],
        'event_end_at' => ['label' => 'Selesai (WIB)', 'type' => 'datetime-local', 'rules' => 'required|date|after:event_start_at'],
        'venue_name' => ['label' => 'Nama lokasi', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'venue_address' => ['label' => 'Alamat', 'type' => 'textarea', 'rules' => 'required|string|max:2000'],
        'description' => ['label' => 'Tentang acara', 'type' => 'textarea', 'rules' => 'required|string|max:5000'],
        'hero_title' => ['label' => 'Judul hero', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'hero_description' => ['label' => 'Deskripsi hero', 'type' => 'textarea', 'rules' => 'required|string|max:2000'],
        'school_image' => ['label' => 'Foto sekolah', 'type' => 'file', 'rules' => 'nullable'],
        'is_active' => ['label' => 'Jadikan edisi aktif', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
    'campuses' => ['label' => 'Kampus', 'model' => Campus::class, 'fields' => [
        'name' => ['label' => 'Nama universitas', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'slug' => ['label' => 'Slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:255'],
        'abbreviation' => ['label' => 'Singkatan', 'type' => 'text', 'rules' => 'required|string|max:20'],
        'city' => ['label' => 'Kota', 'type' => 'text', 'rules' => 'required|string|max:100'],
        'province' => ['label' => 'Provinsi', 'type' => 'text', 'rules' => 'nullable|string|max:100'],
        'official_url' => ['label' => 'Website resmi', 'type' => 'url', 'rules' => 'required|url:http,https|max:255'],
        'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:3000'],
        'logo' => ['label' => 'Logo', 'type' => 'file', 'rules' => 'nullable'],
        'cover_image' => ['label' => 'Foto kampus', 'type' => 'file', 'rules' => 'nullable'],
        'display_order' => ['label' => 'Urutan tampilan', 'type' => 'number', 'rules' => 'required|integer|min:0|max:10000'],
        'is_active' => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
    'members' => ['label' => 'Anggota', 'model' => Member::class, 'fields' => [
        'name' => ['label' => 'Nama lengkap', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'slug' => ['label' => 'Slug', 'type' => 'text', 'rules' => 'required|alpha_dash|max:255'],
        'event_edition_id' => ['label' => 'Edisi acara', 'type' => 'event', 'rules' => 'required|exists:event_editions,id'],
        'campus_id' => ['label' => 'Kampus', 'type' => 'campus', 'rules' => 'required|exists:campuses,id'],
        'photo' => ['label' => 'Foto anggota (4:5)', 'type' => 'file', 'rules' => 'nullable'],
        'faculty' => ['label' => 'Fakultas', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        'major' => ['label' => 'Jurusan', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        'study_program' => ['label' => 'Program studi', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'cohort_year' => ['label' => 'Angkatan', 'type' => 'number', 'rules' => 'nullable|integer|min:1990|max:2100'],
        'bio' => ['label' => 'Biografi', 'type' => 'textarea', 'rules' => 'nullable|string|max:3000'],
        'instagram_url' => ['label' => 'Instagram', 'type' => 'url', 'rules' => 'nullable|url:http,https|max:255'],
        'linkedin_url' => ['label' => 'LinkedIn', 'type' => 'url', 'rules' => 'nullable|url:http,https|max:255'],
        'display_order' => ['label' => 'Urutan tampilan', 'type' => 'number', 'rules' => 'required|integer|min:0|max:10000'],
        'is_active' => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
    'schedules' => ['label' => 'Jadwal', 'model' => Schedule::class, 'fields' => [
        'title' => ['label' => 'Nama kegiatan', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'event_edition_id' => ['label' => 'Edisi acara', 'type' => 'event', 'rules' => 'required|exists:event_editions,id'],
        'event_date' => ['label' => 'Tanggal (WIB)', 'type' => 'date', 'rules' => 'required|date'],
        'start_time' => ['label' => 'Jam mulai', 'type' => 'time', 'rules' => 'required|date_format:H:i'],
        'end_time' => ['label' => 'Jam selesai', 'type' => 'time', 'rules' => 'required|date_format:H:i|after:start_time'],
        'location' => ['label' => 'Lokasi', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        'speaker' => ['label' => 'Pembicara', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        'description' => ['label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:3000'],
        'display_order' => ['label' => 'Urutan tampilan', 'type' => 'number', 'rules' => 'required|integer|min:0|max:10000'],
        'is_active' => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
    'galleries' => ['label' => 'Galeri', 'model' => Gallery::class, 'fields' => [
        'image' => ['label' => 'Foto', 'type' => 'file', 'rules' => 'nullable'],
        'caption' => ['label' => 'Keterangan', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
        'alt_text' => ['label' => 'Deskripsi gambar (aksesibilitas)', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'event_edition_id' => ['label' => 'Edisi acara (opsional)', 'type' => 'event', 'rules' => 'nullable|exists:event_editions,id'],
        'display_order' => ['label' => 'Urutan tampilan', 'type' => 'number', 'rules' => 'required|integer|min:0|max:10000'],
        'is_active' => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
    'faqs' => ['label' => 'FAQ', 'model' => Faq::class, 'fields' => [
        'question' => ['label' => 'Pertanyaan', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'answer' => ['label' => 'Jawaban', 'type' => 'textarea', 'rules' => 'required|string|max:5000'],
        'event_edition_id' => ['label' => 'Edisi acara (opsional)', 'type' => 'event', 'rules' => 'nullable|exists:event_editions,id'],
        'display_order' => ['label' => 'Urutan tampilan', 'type' => 'number', 'rules' => 'required|integer|min:0|max:10000'],
        'is_active' => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
    'partners' => ['label' => 'Partner', 'model' => Partner::class, 'fields' => [
        'name' => ['label' => 'Nama partner', 'type' => 'text', 'rules' => 'required|string|max:255'],
        'logo' => ['label' => 'Logo', 'type' => 'file', 'rules' => 'nullable'],
        'url' => ['label' => 'Website', 'type' => 'url', 'rules' => 'nullable|url:http,https|max:255'],
        'type' => ['label' => 'Jenis partner', 'type' => 'text', 'rules' => 'required|string|max:100'],
        'event_edition_id' => ['label' => 'Edisi acara (opsional)', 'type' => 'event', 'rules' => 'nullable|exists:event_editions,id'],
        'display_order' => ['label' => 'Urutan tampilan', 'type' => 'number', 'rules' => 'required|integer|min:0|max:10000'],
        'is_active' => ['label' => 'Tampilkan di website', 'type' => 'checkbox', 'rules' => 'boolean'],
    ]],
];
