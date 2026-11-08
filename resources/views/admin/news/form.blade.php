@extends('layouts.admin')

@section('title', $isEditing ? 'Edit Berita' : ($isSubmission ? 'Kirim Berita' : 'Tambah Berita'))

@section('content')
    @php
        $saveRoute = $isSubmission
            ? route($routePrefix === 'pengurus' ? 'pengurus.news.store' : 'member.news-submissions.store')
            : ($isEditing ? route('admin.news.update', $article) : route('admin.news.store'));
        $backRoute = $isSubmission
            ? route($routePrefix === 'pengurus' ? 'pengurus.news.index' : 'member.news-submissions.index')
            : route('admin.news.index');
    @endphp
    <div class="admin-content-heading"><div><span class="eyebrow-label">PUBLIKASI MAHAPATI</span><h1>{{ $isEditing ? 'Edit Berita' : ($isSubmission ? 'Kirim Berita atau Publikasi' : 'Tambah Berita') }}</h1><p>Isi artikel dengan jelas dan sertakan foto sampul bila tersedia.</p></div><a class="admin-secondary-button" href="{{ $backRoute }}">← Kembali</a></div>
    @if ($errors->any())<div class="admin-alert error" role="alert">{{ $errors->first() }}</div>@endif
    @if ($isSubmission)<div class="activity-permission-note"><span>ⓘ</span> Kiriman akan ditinjau Admin sebelum diterbitkan. Admin dapat menyetujui atau menolak dengan alasan.</div>@endif

    <form class="dashboard-card admin-card news-form-card" method="POST" enctype="multipart/form-data" action="{{ $saveRoute }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif
        <div class="news-form-layout">
            <div class="news-form-fields">
                <div class="admin-field"><label for="title">Judul berita *</label><input class="admin-input" id="title" name="title" value="{{ old('title', $article->title) }}" maxlength="180" required placeholder="Contoh: MAHAPATI Mengadakan Pendakian Bersama">@error('title')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="category">Kategori *</label><input class="admin-input" id="category" name="category" value="{{ old('category', $article->category) }}" maxlength="80" required placeholder="Berita, Publikasi, atau Pengumuman">@error('category')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="excerpt">Ringkasan *</label><textarea class="admin-textarea news-excerpt-input" id="excerpt" name="excerpt" maxlength="300" required placeholder="Ringkasan singkat yang tampil pada kartu berita.">{{ old('excerpt', $article->excerpt) }}</textarea><small>Maksimal 300 karakter.</small>@error('excerpt')<span class="admin-error">{{ $message }}</span>@enderror</div>
                <div class="admin-field"><label for="body">Isi berita *</label><textarea class="admin-textarea news-body-input" id="body" name="body" maxlength="30000" required placeholder="Tuliskan isi berita atau publikasi secara lengkap.">{{ old('body', $article->body) }}</textarea><small>Gunakan paragraf yang jelas. Format baris akan dipertahankan pada halaman publik.</small>@error('body')<span class="admin-error">{{ $message }}</span>@enderror</div>
            </div>
            <aside class="news-form-aside">
                <div class="admin-field"><label for="cover_image">Foto sampul (opsional)</label><input class="admin-input" type="file" id="cover_image" name="cover_image" accept=".jpg,.jpeg,.png,.webp"><small>JPG, PNG, atau WebP; maksimal 2 MB.</small>@error('cover_image')<span class="admin-error">{{ $message }}</span>@enderror</div>
                @if ($article->cover_image)
                    <img class="news-form-preview" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($article->cover_image) }}" alt="Sampul berita saat ini">
                    @if ($isEditing)<label class="news-remove-cover"><input type="checkbox" name="remove_cover" value="1" @checked(old('remove_cover'))> Hapus foto sampul saat disimpan</label>@endif
                @endif
                @unless ($isSubmission)
                    <div class="admin-field"><label for="status">Status artikel *</label><select class="admin-select" id="status" name="status" required><option value="draft" @selected(old('status', $article->status) === 'draft')>Simpan sebagai draft</option><option value="published" @selected(old('status', $article->status) === 'published')>Terbitkan sekarang</option></select>@error('status')<span class="admin-error">{{ $message }}</span>@enderror</div>
                @endunless
                <div class="news-form-guidance"><strong>Sebelum menyimpan</strong><span>Pastikan isi berita sudah benar dan foto sampul layak dipublikasikan. Konten kiriman anggota menunggu persetujuan Admin.</span></div>
            </aside>
        </div>
        <div class="admin-button-row news-form-actions"><button class="admin-action-button" type="submit">{{ $isSubmission ? 'Kirim untuk Persetujuan' : ($isEditing ? 'Simpan Perubahan' : 'Simpan Berita') }}</button><a class="admin-secondary-button" href="{{ $backRoute }}">Batal</a></div>
    </form>
@endsection
