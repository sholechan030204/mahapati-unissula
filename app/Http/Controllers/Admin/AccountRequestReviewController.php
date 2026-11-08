<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\Member;
use App\Models\User;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccountRequestReviewController extends Controller
{
    public function index(): View
    {
        $accountRequests = AccountRequest::query()
            ->with(['reviewer', 'approvedUser'])
            ->latest()
            ->paginate(20);

        return view('admin.account-requests.index', [
            'accountRequests' => $accountRequests,
        ]);
    }

    public function approve(Request $request, AccountRequest $accountRequest): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:anggota,pengurus'],
        ]);

        $approvedUser = DB::transaction(function () use ($request, $accountRequest, $validated): ?User {
            $lockedRequest = AccountRequest::query()
                ->whereKey($accountRequest->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->status !== AccountRequest::STATUS_PENDING) {
                return null;
            }

            $memberQuery = Member::query();

            if (filled($lockedRequest->nra)) {
                $memberQuery->where('nra', $lockedRequest->nra);
            }

            if (filled($lockedRequest->nim)) {
                $memberQuery->where('nim', $lockedRequest->nim);
            }

            $member = $memberQuery->lockForUpdate()->first();

            if ($member === null || $member->user_id !== null) {
                return null;
            }

            if (User::query()->where('email', $lockedRequest->email)->exists()) {
                return null;
            }

            $user = User::query()->create([
                'name' => $lockedRequest->name,
                'email' => $lockedRequest->email,
                'password' => Str::random(64),
                'role' => $validated['role'],
            ]);

            $member->update(['user_id' => $user->id]);

            $lockedRequest->update([
                'status' => AccountRequest::STATUS_APPROVED,
                'reviewed_by' => $request->user()->id,
                'approved_user_id' => $user->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            return $user;
        });

        if ($approvedUser === null) {
            return back()->withErrors([
                'review' => 'Pengajuan sudah diproses, email telah dipakai, atau anggota belum terdaftar/cocok pada Manajemen Anggota. Pastikan data anggota dan NRA/NIM cocok, lalu muat ulang daftar.',
            ]);
        }

        $token = Password::broker()->createToken($approvedUser);
        DashboardNotifier::user($approvedUser, 'Pengajuan akun disetujui', 'Akun MAHAPATI Anda sudah dibuat. Silakan buat password dan masuk ke sistem.', route('login'));
        $passwordSetupUrl = route('password.reset', [
            'token' => $token,
            'email' => $approvedUser->email,
        ]);

        if (config('mail.default') !== 'log') {
            try {
                $approvedUser->sendPasswordResetNotification($token);

                return back()->with('status', 'Akun disetujui. Tautan untuk membuat password sudah dikirim ke email pemohon.');
            } catch (\Throwable) {
                return back()->with([
                    'status' => 'Akun disetujui, tetapi email belum berhasil dikirim. Salin tautan pembuatan password dan sampaikan secara pribadi kepada pemohon.',
                    'passwordSetupUrl' => $passwordSetupUrl,
                ]);
            }
        }

        return back()->with([
            'status' => 'Akun disetujui. Layanan email belum dikonfigurasi; salin tautan pembuatan password dan sampaikan secara pribadi kepada pemohon.',
            'passwordSetupUrl' => $passwordSetupUrl,
        ]);
    }

    public function createPasswordLink(AccountRequest $accountRequest): RedirectResponse
    {
        abort_unless($accountRequest->status === AccountRequest::STATUS_APPROVED, 404);

        $user = $accountRequest->approvedUser;

        if ($user === null) {
            return back()->withErrors([
                'review' => 'Akun yang terhubung dengan pengajuan ini tidak ditemukan. Periksa data akun anggota terlebih dahulu.',
            ]);
        }

        $token = Password::broker()->createToken($user);
        $passwordSetupUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return back()->with([
            'status' => 'Tautan baru untuk membuat password sudah dibuat. Salin dan sampaikan secara pribadi kepada pemilik akun.',
            'passwordSetupUrl' => $passwordSetupUrl,
        ]);
    }

    public function reject(Request $request, AccountRequest $accountRequest): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $wasRejected = DB::transaction(function () use ($request, $accountRequest, $validated): bool {
            $lockedRequest = AccountRequest::query()
                ->whereKey($accountRequest->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedRequest->status !== AccountRequest::STATUS_PENDING) {
                return false;
            }

            $lockedRequest->update([
                'status' => AccountRequest::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return true;
        });

        if (! $wasRejected) {
            return back()->withErrors([
                'review' => 'Pengajuan ini sudah diproses. Muat ulang daftar untuk melihat status terbaru.',
            ]);
        }

        return back()->with('status', 'Pengajuan ditolak. Alasan penolakan tersimpan pada riwayat pengajuan.');
    }
}
