@extends('admin.auth-layout')
@section('title','Kembali terhubung.')
@section('description','Masukkan email admin untuk menerima tautan pemulihan kata sandi.')
@section('content')<form action="/admin/forgot-password" method="post" class="auth-form">@csrf<div class="form-field"><label for="email">Email panitia</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></div><button class="button">Kirim Tautan Pemulihan <x-icon name="arrow-right"/></button><a class="inline-link" href="/admin/login">Kembali ke halaman masuk</a></form>@endsection
