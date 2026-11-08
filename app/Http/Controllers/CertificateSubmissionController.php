<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Certificate;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CertificateSubmissionController extends Controller
{
    private const MAX_PDF_KILOBYTES = 5120;

    public function create(Request $request): View
    {
        abort_unless($request->user()->member !== null, 403, 'Akun Anda belum terhubung dengan data anggota MAHAPATI.');

        return view('certificates.create', [
            'activities' => Activity::query()->orderByDesc('planned_at')->get(['id', 'title', 'planned_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $member = $user->member;
        abort_unless($member !== null, 403, 'Akun Anda belum terhubung dengan data anggota MAHAPATI.');

        $validated = $request->validate([
            'activity_id' => ['required', 'integer', Rule::exists('activities', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'issued_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:'.self::MAX_PDF_KILOBYTES],
        ]);

        $certificate = Certificate::query()->create([
            'member_id' => $member->id,
            'activity_id' => $validated['activity_id'],
            'title' => $validated['title'],
            'certificate_number' => $validated['certificate_number'] ?? null,
            'issued_at' => $validated['issued_at'],
            'file_path' => $request->file('pdf')->store('certificates/pending', 'local'),
            'notes' => $validated['notes'] ?? null,
            'created_by' => $user->id,
            'status' => Certificate::STATUS_PENDING,
        ]);

        DashboardNotifier::admins(
            'Pengajuan sertifikat baru',
            $user->name.' mengajukan sertifikat untuk ditinjau.',
            route('admin.certificates.show', $certificate),
        );

        return redirect()->route($this->mineRoute($user->role))->with('status', 'Pengajuan sertifikat berhasil dikirim. Sertifikat akan tersedia setelah disetujui Admin.');
    }

    private function mineRoute(string $role): string
    {
        return $role === 'pengurus' ? 'pengurus.certificates.mine' : 'member.certificates.mine';
    }
}
