@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <div class="admin-content-heading"><div><span class="eyebrow-label">KONFIGURASI WEBSITE PUBLIK</span><h1>Pengaturan</h1><p>Kelola kontak dan tautan media sosial resmi MAHAPATI.</p></div></div>
    @if (session('status'))<div class="admin-alert" role="status">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    <section class="dashboard-card admin-tool-card admin-tool-form-card">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="admin-tool-form">
            @csrf @method('PUT')
            <div class="tool-form-intro"><h2>Kontak resmi dan media sosial</h2><p>Perubahan akan terlihat di halaman Kontak dan bagian website publik yang menampilkan kontak atau tautan sosial.</p></div>
            <div class="admin-form-grid">
                <label class="admin-field"><span>Email organisasi</span><input class="admin-input" type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" required>@error('contact_email')<small class="admin-error">{{ $message }}</small>@enderror</label>
                <label class="admin-field"><span>WhatsApp organisasi</span><input class="admin-input" type="tel" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp']) }}" placeholder="Kosongkan jika belum tersedia">@error('contact_whatsapp')<small class="admin-error">{{ $message }}</small>@enderror</label>
                <label class="admin-field"><span>Instagram</span><input class="admin-input" type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}" required>@error('instagram_url')<small class="admin-error">{{ $message }}</small>@enderror</label>
                <label class="admin-field"><span>Facebook</span><input class="admin-input" type="url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}" required>@error('facebook_url')<small class="admin-error">{{ $message }}</small>@enderror</label>
                <label class="admin-field"><span>YouTube</span><input class="admin-input" type="url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url']) }}" required>@error('youtube_url')<small class="admin-error">{{ $message }}</small>@enderror</label>
                <label class="admin-field"><span>TikTok</span><input class="admin-input" type="url" name="tiktok_url" value="{{ old('tiktok_url', $settings['tiktok_url']) }}" required>@error('tiktok_url')<small class="admin-error">{{ $message }}</small>@enderror</label>
            </div>
            <div class="admin-button-row"><button class="admin-action-button" type="submit">Simpan Pengaturan</button></div>
        </form>
    </section>
@endsection
