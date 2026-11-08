<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountRequestController;
use App\Http\Controllers\AchievementSubmissionController;
use App\Http\Controllers\ArticleSubmissionController;
use App\Http\Controllers\Admin\AccountRequestReviewController;
use App\Http\Controllers\Admin\AchievementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CashTransactionController;
use App\Http\Controllers\Admin\GalleryChangeRequestController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GalleryManagementController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ActivityChangeRequestController;
use App\Http\Controllers\Admin\ActivityManagementController;
use App\Http\Controllers\Admin\WorkProgramController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Member\ActivityController as MemberActivityController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\GallerySubmissionController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Member\AccountSettingsController as MemberAccountSettingsController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\RegistrationManagementController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AdminToolsController;
use App\Http\Controllers\CertificateAccessController;
use App\Http\Controllers\CertificateSubmissionController;
use App\Http\Controllers\NotificationCenterController;
use App\Http\Controllers\MemberDomicileController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\PublicAchievementController;
use App\Http\Controllers\Pengurus\ActivityProposalController;
use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboardController;
use App\Http\Controllers\Pengurus\GalleryProposalController;
use App\Http\Controllers\Pengurus\ProfileController as PengurusProfileController;
use App\Models\Activity;
use App\Models\Article;
use App\Models\GalleryItem;
use App\Models\RegistrationSetting;
use Illuminate\View\View;

Route::get('/', function (): View {
    return view('public.home', [
        'latestActivities' => Activity::query()->with('workProgram')->latest('planned_at')->limit(3)->get(),
        'galleryPreview' => GalleryItem::query()->latest()->limit(5)->get(),
        'latestArticles' => Article::query()->where('status', Article::STATUS_PUBLISHED)->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at')->limit(3)->get(),
        'registration' => RegistrationSetting::publicData(),
    ]);
})->name('home');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/pengajuan-akun', [AccountRequestController::class, 'create'])->name('account-request.create');
    Route::post('/pengajuan-akun', [AccountRequestController::class, 'store'])->middleware('throttle:5,10')->name('account-request.store');
    Route::get('/lupa-password', [PasswordController::class, 'requestForm'])->name('password.request');
    Route::post('/lupa-password', [PasswordController::class, 'sendResetLink'])->middleware('throttle:3,10')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'reset'])->middleware('throttle:5,10')->name('password.update');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.audit'])->group(function (): void {
    Route::get('/audit-log', [AdminToolsController::class, 'auditIndex'])->name('audit.index');
    Route::get('/backup-export', [AdminToolsController::class, 'backupIndex'])->name('backup.index');
    Route::get('/backup-export/download', [AdminToolsController::class, 'downloadBackup'])->name('backup.download');
    Route::get('/backup-export/csv', [AdminToolsController::class, 'exportCsv'])->name('backup.csv');
    Route::post('/backup-export/restore', [AdminToolsController::class, 'restoreBackup'])->name('backup.restore');
    Route::get('/pengaturan', [AdminToolsController::class, 'settings'])->name('settings.edit');
    Route::put('/pengaturan', [AdminToolsController::class, 'updateSettings'])->name('settings.update');
    Route::get('/profil', [AdminToolsController::class, 'profile'])->name('profile.edit');
    Route::put('/profil', [AdminToolsController::class, 'updateProfile'])->name('profile.update');
    Route::get('/domisili', [MemberDomicileController::class, 'index'])->name('domiciles.index');
    Route::delete('/domisili/{member}', [MemberDomicileController::class, 'destroy'])->name('domiciles.destroy');
    Route::get('/notifikasi', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/{notification}/buka', [NotificationCenterController::class, 'open'])->name('notifications.open');
    Route::post('/notifikasi/{notification}/baca', [NotificationCenterController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifikasi/baca-semua', [NotificationCenterController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/pendaftaran', [RegistrationManagementController::class, 'index'])->name('registration.index');
    Route::put('/pendaftaran', [RegistrationManagementController::class, 'update'])->name('registration.update');
    Route::delete('/pendaftaran/pendaftar/{applicant}', [RegistrationManagementController::class, 'destroyApplicant'])->name('registration.applicants.destroy');
    Route::resource('kas', CashTransactionController::class)->parameters(['kas' => 'cashTransaction'])->names('cash-transactions');
    Route::post('/berita/{article}/approve', [NewsController::class, 'approve'])->name('news.approve');
    Route::post('/berita/{article}/reject', [NewsController::class, 'reject'])->name('news.reject');
    Route::resource('berita', NewsController::class)->parameters(['berita' => 'article'])->names('news');
    Route::post('/prestasi/{achievement}/approve', [AchievementController::class, 'approve'])->name('achievements.approve');
    Route::post('/prestasi/{achievement}/reject', [AchievementController::class, 'reject'])->name('achievements.reject');
    Route::resource('prestasi', AchievementController::class)->parameters(['prestasi' => 'achievement'])->names('achievements');
    Route::put('/anggota/{member}/role', [MemberController::class, 'updateRole'])->name('members.role.update');
    Route::resource('anggota', MemberController::class)->except(['destroy'])->parameters(['anggota' => 'member'])->names('members');
    Route::get('/kegiatan', [ActivityManagementController::class, 'index'])->name('activities.index');
    Route::resource('kegiatan', AdminActivityController::class)->except(['index'])->parameters(['kegiatan' => 'activity'])->names('activities');
    Route::resource('program-kerja', WorkProgramController::class)->except(['index'])->parameters(['program-kerja' => 'workProgram'])->names('work-programs');
    Route::get('/galeri', [GalleryManagementController::class, 'index'])->name('gallery.index');
    Route::resource('galeri', GalleryController::class)->except(['index'])->parameters(['galeri' => 'galleryItem'])->names('gallery');
    Route::get('/persetujuan-galeri', [GalleryChangeRequestController::class, 'index'])->name('gallery-requests.index');
    Route::get('/persetujuan-galeri/{galleryChangeRequest}/foto', [GalleryChangeRequestController::class, 'photo'])->name('gallery-requests.photo');
    Route::post('/persetujuan-galeri/{galleryChangeRequest}/setujui', [GalleryChangeRequestController::class, 'approve'])->name('gallery-requests.approve');
    Route::post('/persetujuan-galeri/{galleryChangeRequest}/tolak', [GalleryChangeRequestController::class, 'reject'])->name('gallery-requests.reject');
    Route::get('/persetujuan-kegiatan', [ActivityChangeRequestController::class, 'index'])->name('activity-requests.index');
    Route::post('/persetujuan-kegiatan/{activityChangeRequest}/setujui', [ActivityChangeRequestController::class, 'approve'])->name('activity-requests.approve');
    Route::post('/persetujuan-kegiatan/{activityChangeRequest}/tolak', [ActivityChangeRequestController::class, 'reject'])->name('activity-requests.reject');
    Route::get('/pengajuan-akun', [AccountRequestReviewController::class, 'index'])->name('account-requests.index');
    Route::post('/pengajuan-akun/{accountRequest}/setujui', [AccountRequestReviewController::class, 'approve'])->name('account-requests.approve');
    Route::post('/pengajuan-akun/{accountRequest}/tautan-password', [AccountRequestReviewController::class, 'createPasswordLink'])->name('account-requests.password-link');
    Route::post('/pengajuan-akun/{accountRequest}/tolak', [AccountRequestReviewController::class, 'reject'])->name('account-requests.reject');
    Route::resource('sertifikat', CertificateController::class)->parameters(['sertifikat' => 'certificate'])->names('certificates');
    Route::post('/sertifikat/{certificate}/setujui', [CertificateController::class, 'approve'])->name('certificates.approve');
    Route::post('/sertifikat/{certificate}/tolak', [CertificateController::class, 'reject'])->name('certificates.reject');
    Route::get('/sertifikat/{certificate}/download', [CertificateAccessController::class, 'download'])->name('certificates.download');
});
Route::prefix('pengurus')->name('pengurus.')->middleware(['auth', 'role:pengurus'])->group(function (): void {
    Route::get('/dashboard', PengurusDashboardController::class)->name('dashboard');
    Route::get('/profil', [PengurusProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [PengurusProfileController::class, 'update'])->name('profile.update');
    Route::get('/notifikasi', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/{notification}/buka', [NotificationCenterController::class, 'open'])->name('notifications.open');
    Route::post('/notifikasi/{notification}/baca', [NotificationCenterController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifikasi/baca-semua', [NotificationCenterController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/sertifikat-saya', [CertificateAccessController::class, 'mine'])->name('certificates.mine');
    Route::get('/sertifikat-saya/ajukan', [CertificateSubmissionController::class, 'create'])->name('certificates.create');
    Route::post('/sertifikat-saya', [CertificateSubmissionController::class, 'store'])->middleware('throttle:5,10')->name('certificates.store');
    Route::get('/sertifikat-saya/{certificate}/download', [CertificateAccessController::class, 'download'])->name('certificates.download');
    Route::get('/pendaftaran', [RegistrationManagementController::class, 'index'])->name('registration.index');
    Route::put('/pendaftaran', [RegistrationManagementController::class, 'update'])->name('registration.update');
    Route::get('/prestasi', [AchievementSubmissionController::class, 'index'])->name('achievements.index');
    Route::get('/prestasi/create', [AchievementSubmissionController::class, 'create'])->name('achievements.create');
    Route::post('/prestasi', [AchievementSubmissionController::class, 'store'])->name('achievements.store');
    Route::get('/berita', [ArticleSubmissionController::class, 'index'])->name('news.index');
    Route::get('/berita/create', [ArticleSubmissionController::class, 'create'])->name('news.create');
    Route::post('/berita', [ArticleSubmissionController::class, 'store'])->name('news.store');
    Route::get('/kegiatan', [ActivityManagementController::class, 'index'])->name('activities.index');
    Route::get('/kegiatan/create', [ActivityProposalController::class, 'createActivity'])->name('activities.create');
    Route::post('/kegiatan', [ActivityProposalController::class, 'storeActivity'])->name('activities.store');
    Route::get('/kegiatan/{activity}', [ActivityManagementController::class, 'showActivity'])->name('activities.show');
    Route::get('/kegiatan/{activity}/edit', [ActivityProposalController::class, 'editActivity'])->name('activities.edit');
    Route::put('/kegiatan/{activity}', [ActivityProposalController::class, 'updateActivity'])->name('activities.update');
    Route::delete('/kegiatan/{activity}', [ActivityProposalController::class, 'destroyActivity'])->name('activities.destroy');
    Route::get('/program-kerja/create', [ActivityProposalController::class, 'createProgram'])->name('work-programs.create');
    Route::post('/program-kerja', [ActivityProposalController::class, 'storeProgram'])->name('work-programs.store');
    Route::get('/program-kerja/{workProgram}', [ActivityManagementController::class, 'showProgram'])->name('work-programs.show');
    Route::get('/program-kerja/{workProgram}/edit', [ActivityProposalController::class, 'editProgram'])->name('work-programs.edit');
    Route::put('/program-kerja/{workProgram}', [ActivityProposalController::class, 'updateProgram'])->name('work-programs.update');
    Route::delete('/program-kerja/{workProgram}', [ActivityProposalController::class, 'destroyProgram'])->name('work-programs.destroy');
    Route::get('/galeri', [GalleryManagementController::class, 'index'])->name('gallery.index');
    Route::get('/galeri/create', [GalleryProposalController::class, 'create'])->name('gallery.create');
    Route::post('/galeri', [GalleryProposalController::class, 'store'])->name('gallery.store');
    Route::get('/galeri/{galleryItem}/edit', [GalleryProposalController::class, 'edit'])->name('gallery.edit');
    Route::put('/galeri/{galleryItem}', [GalleryProposalController::class, 'update'])->name('gallery.update');
    Route::delete('/galeri/{galleryItem}', [GalleryProposalController::class, 'destroy'])->name('gallery.destroy');
    Route::get('/galeri/{galleryItem}', [GalleryManagementController::class, 'show'])->name('gallery.show');
    Route::get('/anggota', [MemberController::class, 'index'])->name('members.index');
    Route::get('/anggota/{member}', [MemberController::class, 'show'])->name('members.show');
});
Route::prefix('anggota')->name('member.')->middleware(['auth', 'role:anggota'])->group(function (): void {
    Route::get('/dashboard', MemberDashboardController::class)->name('dashboard');
    Route::get('/profil-saya', [MemberProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil-saya', [MemberProfileController::class, 'update'])->name('profile.update');
    Route::get('/pengaturan-akun', [MemberAccountSettingsController::class, 'edit'])->name('account-settings.edit');
    Route::put('/pengaturan-akun', [MemberAccountSettingsController::class, 'update'])->name('account-settings.update');
    Route::get('/akun', [MemberAccountSettingsController::class, 'show'])->name('account.show');
    Route::get('/domisili-saya', [MemberDomicileController::class, 'editMine'])->name('domicile.edit');
    Route::put('/domisili-saya', [MemberDomicileController::class, 'updateMine'])->name('domicile.update');
    Route::get('/notifikasi', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/{notification}/buka', [NotificationCenterController::class, 'open'])->name('notifications.open');
    Route::post('/notifikasi/{notification}/baca', [NotificationCenterController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifikasi/baca-semua', [NotificationCenterController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/sertifikat-saya', [CertificateAccessController::class, 'mine'])->name('certificates.mine');
    Route::get('/sertifikat-saya/ajukan', [CertificateSubmissionController::class, 'create'])->name('certificates.create');
    Route::post('/sertifikat-saya', [CertificateSubmissionController::class, 'store'])->middleware('throttle:5,10')->name('certificates.store');
    Route::get('/sertifikat-saya/{certificate}/download', [CertificateAccessController::class, 'download'])->name('certificates.download');
    Route::get('/kirim-konten/prestasi/saya', [AchievementSubmissionController::class, 'index'])->name('achievement-submissions.index');
    Route::get('/kirim-konten/prestasi', [AchievementSubmissionController::class, 'create'])->name('achievement-submissions.create');
    Route::post('/kirim-konten/prestasi', [AchievementSubmissionController::class, 'store'])->name('achievement-submissions.store');
    Route::get('/kirim-konten/berita/saya', [ArticleSubmissionController::class, 'index'])->name('news-submissions.index');
    Route::get('/kirim-konten/berita', [ArticleSubmissionController::class, 'create'])->name('news-submissions.create');
    Route::post('/kirim-konten/berita', [ArticleSubmissionController::class, 'store'])->name('news-submissions.store');
    Route::get('/kegiatan', [MemberActivityController::class, 'index'])->name('activities.index');
    Route::get('/kegiatan/{activity}', [MemberActivityController::class, 'showActivity'])->name('activities.show');
    Route::get('/program-kerja/{workProgram}', [MemberActivityController::class, 'showProgram'])->name('work-programs.show');
    Route::get('/kirim-konten', [GallerySubmissionController::class, 'index'])->name('gallery-submissions.index');
    Route::get('/kirim-konten/galeri', [GallerySubmissionController::class, 'create'])->name('gallery-submissions.create');
    Route::post('/kirim-konten/galeri', [GallerySubmissionController::class, 'store'])->name('gallery-submissions.store');
});
Route::view('/profil', 'public.profile')->name('profile');
Route::view('/divisi', 'public.divisions')->name('divisions');
Route::get('/kegiatan', function (): View {
    return view('public.activities', [
        'featuredActivity' => Activity::query()->with('workProgram')->where('is_featured', true)->latest('updated_at')->first(),
        'activities' => Activity::query()->with('workProgram')->latest('planned_at')->paginate(9),
    ]);
})->name('activities');
Route::get('/kegiatan/{activity}', function (Activity $activity): View {
    $activity->load('workProgram');

    return view('public.activity-detail', ['activity' => $activity]);
})->name('activities.show');
Route::get('/galeri', function (): View {
    return view('public.gallery', [
        'galleryItems' => GalleryItem::query()->latest()->paginate(15),
    ]);
})->name('gallery');
Route::view('/kontak', 'public.contact')->name('contact');

Route::get('/pendaftaran', [RegistrationController::class, 'show'])->name('registration');
Route::post('/pendaftaran', [RegistrationController::class, 'store'])->middleware('throttle:5,10')->name('registration.store');

Route::get('/prestasi', [PublicAchievementController::class, 'index'])->name('achievements.index');
Route::get('/prestasi/{slug}', [PublicAchievementController::class, 'show'])->name('achievements.show');

Route::get('/berita', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [PublicNewsController::class, 'show'])->name('news.show');

Route::get('/divisi/{slug}', function (string $slug) {
    $divisions = config('divisions');
    $division = $divisions[$slug] ?? null;

    abort_unless($division !== null, 404);

    return view('public.division-detail', [
        'division' => $division,
    ]);
})->name('divisions.show');
