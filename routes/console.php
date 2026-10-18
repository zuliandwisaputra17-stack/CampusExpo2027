<?php

use App\Models\EventEdition;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('expo:admin {email} {--name=Administrator}', function () {
    $email = $this->argument('email');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $this->error('Email tidak valid.');
    }
    $password = $this->secret('Kata sandi baru (minimal 12 karakter)');
    if (strlen($password) < 12) {
        return $this->error('Minimal 12 karakter.');
    }
    $user = User::firstOrNew(['email' => $email]);
    $user->name = $this->option('name');
    $user->password = $password;
    $user->is_admin = true;
    $user->save();
    $this->info('Akun admin siap digunakan.');
})->purpose('Buat atau perbarui akun admin Campus Expo secara aman');
Artisan::command('expo:seed-demo', function () {
    if (EventEdition::exists() || SiteSetting::exists()) {
        return $this->info('Data sudah ada; konten tidak diubah.');
    }
    $this->call('db:seed', ['--force' => true]);
})->purpose('Isi konten contoh hanya pada database yang masih kosong');
