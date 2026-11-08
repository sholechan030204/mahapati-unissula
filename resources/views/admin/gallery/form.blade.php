@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Materi Galeri' : 'Tambah Materi Galeri')

@section('content')
    @php
        $saveRoute = $routePrefix === 'member'
            ? route('member.gallery-submissions.store')
            : ($isEditing ? route($routePrefix.'.gallery.update', $galleryItem) : route($routePrefix.'.gallery.store'));
        $backRoute = $routePrefix === 'member' ? route('member.gallery-submissions.index') : route($routePrefix.'.gallery.index');
    @endphp
    <div class="admin-content-heading"><div><span class="eyebrow-label">DOKUMENTASI MAHAPATI</span><h1>{{ $isEditing ? 'Edit Materi Galeri' : ($routePrefix === 'member' ? 'Kirim Konten Galeri' : ($isProposal ? 'Usulkan Materi Galeri' : 'Tambah Materi Galeri')) }}</h1><p>Masukkan foto dokumentasi atau tautan video YouTube yang berkaitan dengan kegiatan MAHAPATI.</p></div><a class="admin-secondary-button" href="{{ $backRoute }}">← Kembali</a></div>
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    @if ($isProposal)<div class="activity-permission-note"><span>ⓘ</span> Kiriman akan diperiksa Admin. Foto atau tautan video baru tampil di halaman publik setelah disetujui.</div>@endif

    <form class="dashboard-card admin-card gallery-form-card" method="POST" enctype="multipart/form-data" action="{{ $saveRoute }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="gallery-form-layout">
            <div class="gallery-form-fields">
                <div class="admin-field"><label for="title">Judul materi *</label><input class="admin-input" id="title" name="title" value="{{ old('title', $galleryItem->title) }}" maxlength="180" required placeholder="Contoh: Pendakian Bersama MAHAPATI">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="caption">Keterangan</label><textarea class="admin-textarea" id="caption" name="caption" maxlength="3000" placeholder="Ceritakan singkat momen atau kegiatan dalam dokumentasi ini.">{{ old('caption', $galleryItem->caption) }}</textarea><small>Maksimal 3.000 karakter.</small>@error('caption')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-form-grid gallery-field-grid">
                    <div class="admin-field"><label for="media_type">Jenis media *</label><select class="admin-select" id="media_type" name="media_type" required><option value="photo" @selected(old('media_type', $galleryItem->media_type) === 'photo')>Foto</option><option value="youtube" @selected(old('media_type', $galleryItem->media_type) === 'youtube')>Video YouTube</option></select>@error('media_type')<span class="admin-error">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="activity_date">Tanggal kegiatan</label><input class="admin-input" type="date" id="activity_date" name="activity_date" value="{{ old('activity_date', $galleryItem->activity_date?->format('Y-m-d')) }}">@error('activity_date')<span class="admin-error">{{ $message }}</span>@enderror</div>
                </div>
                <div class="admin-field gallery-photo-field" data-media-panel="photo"><label for="photo">Unggah foto {{ $isEditing && $galleryItem->photo_path ? '(opsional jika ingin diganti)' : '*' }}</label><input class="admin-input" type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp"><small>JPG, PNG, atau WebP; ukuran maksimal 2 MB.</small>@if ($galleryItem->photo_path)<img class="gallery-form-preview" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($galleryItem->photo_path) }}" alt="Foto {{ $galleryItem->title }} saat ini">@endif @error('photo')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field gallery-youtube-field" data-media-panel="youtube"><label for="youtube_url">Tautan video YouTube *</label><input class="admin-input" type="url" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $galleryItem->youtube_url) }}" maxlength="500" placeholder="https://youtu.be/..." inputmode="url"><small>Tempel tautan YouTube biasa, Shorts, atau youtu.be. Video tidak diunggah ke server.</small>@error('youtube_url')<span class="admin-error">{{ $message }}</span>@enderror</div>
            </div>
            <aside class="gallery-form-guidance"><span class="gallery-guidance-icon">▧</span><h2>Materi yang baik</h2><ul><li>Gunakan foto yang jelas dan relevan dengan kegiatan.</li><li>Pastikan foto layak dipublikasikan dan mendapat izin.</li><li>Untuk video, gunakan tautan YouTube resmi.</li><li>Tulis keterangan singkat agar dokumentasi mudah dipahami.</li></ul></aside>
        </div>
        <div class="admin-button-row gallery-form-actions"><button class="admin-action-button" type="submit">{{ $isProposal ? 'Kirim untuk Persetujuan' : ($isEditing ? 'Simpan Perubahan' : 'Terbitkan ke Galeri') }}</button><a class="admin-secondary-button" href="{{ $backRoute }}">Batal</a></div>
    </form>
    <script>
        (() => {
            const mediaType = document.getElementById('media_type');
            const photoInput = document.getElementById('photo');
            const youtubeInput = document.getElementById('youtube_url');
            const panels = document.querySelectorAll('[data-media-panel]');
            const syncMediaFields = () => {
                const selectedType = mediaType?.value;
                panels.forEach((panel) => {
                    const isVisible = panel.dataset.mediaPanel === selectedType;
                    panel.hidden = !isVisible;
                });
                if (photoInput) photoInput.required = selectedType === 'photo' && !{{ $isEditing && $galleryItem->photo_path ? 'true' : 'false' }};
                if (youtubeInput) youtubeInput.required = selectedType === 'youtube';
            };
            mediaType?.addEventListener('change', syncMediaFields);
            syncMediaFields();
        })();
    </script>
@endsection
