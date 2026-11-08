<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use App\Models\Certificate;
use App\Models\Member;
use App\Support\DashboardNotifier;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in([Certificate::STATUS_PENDING, Certificate::STATUS_APPROVED, Certificate::STATUS_REJECTED])],
        ]);
        $certificates = Certificate::query()->with(['member:id,full_name,nra', 'activity:id,title,planned_at'])
            ->when(($filters['status'] ?? '') !== '', fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->when(($filters['q'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $term = '%'.$filters['q'].'%';
                $query->where(function (Builder $search) use ($term): void {
                    $search->where('title', 'like', $term)->orWhere('certificate_number', 'like', $term)
                        ->orWhereHas('member', fn (Builder $member): Builder => $member->where('full_name', 'like', $term)->orWhere('nra', 'like', $term))
                        ->orWhereHas('activity', fn (Builder $activity): Builder => $activity->where('title', 'like', $term));
                });
            })->latest('issued_at')->latest()->paginate(12)->withQueryString();

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'filters' => $filters,
            'pendingCount' => Certificate::query()->where('status', Certificate::STATUS_PENDING)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.certificates.form', ['certificate' => new Certificate(['issued_at' => today()]), 'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name', 'nra']), 'activities' => Activity::query()->orderByDesc('planned_at')->get(['id', 'title', 'planned_at']), 'isEditing' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCertificate($request);
        $validated['file_path'] = $request->file('pdf')->store('certificates', 'local');
        $validated['created_by'] = $request->user()->id;
        $validated['status'] = Certificate::STATUS_APPROVED;
        $validated['reviewed_by'] = $request->user()->id;
        $validated['reviewed_at'] = now();
        $certificate = Certificate::query()->create($validated);

        return redirect()->route('admin.certificates.show', $certificate)->with('status', 'Sertifikat berhasil diterbitkan.');
    }

    public function show(Certificate $certificate): View
    {
        return view('admin.certificates.show', ['certificate' => $certificate->load(['member.user', 'activity', 'createdBy', 'reviewedBy'])]);
    }

    public function approve(Request $request, Certificate $certificate): RedirectResponse
    {
        $certificate = DB::transaction(function () use ($request, $certificate): Certificate {
            $lockedCertificate = Certificate::query()->whereKey($certificate->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedCertificate->status === Certificate::STATUS_PENDING, 409, 'Pengajuan sertifikat ini sudah ditinjau.');

            $lockedCertificate->update([
                'status' => Certificate::STATUS_APPROVED,
                'rejection_reason' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return $lockedCertificate;
        });

        $certificate->load(['createdBy', 'member.user']);
        $owner = $certificate->createdBy ?? $certificate->member?->user;
        $mineRoute = $owner?->role === 'pengurus' ? 'pengurus.certificates.mine' : 'member.certificates.mine';
        DashboardNotifier::user($owner, 'Pengajuan sertifikat disetujui', 'Sertifikat “'.$certificate->title.'” telah disetujui dan tersedia di menu Sertifikat Saya.', route($mineRoute));

        return redirect()->route('admin.certificates.show', $certificate)->with('status', 'Pengajuan sertifikat disetujui. Sertifikat sekarang tersedia bagi pemiliknya.');
    }

    public function reject(Request $request, Certificate $certificate): RedirectResponse
    {
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:2000']]);
        $certificate = DB::transaction(function () use ($request, $certificate, $validated): Certificate {
            $lockedCertificate = Certificate::query()->whereKey($certificate->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedCertificate->status === Certificate::STATUS_PENDING, 409, 'Pengajuan sertifikat ini sudah ditinjau.');

            $lockedCertificate->update([
                'status' => Certificate::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return $lockedCertificate;
        });

        $certificate->load(['createdBy', 'member.user']);
        $owner = $certificate->createdBy ?? $certificate->member?->user;
        $mineRoute = $owner?->role === 'pengurus' ? 'pengurus.certificates.mine' : 'member.certificates.mine';
        DashboardNotifier::user($owner, 'Pengajuan sertifikat ditolak', 'Sertifikat “'.$certificate->title.'” belum disetujui. Alasan Admin: '.$validated['rejection_reason'], route($mineRoute));

        return redirect()->route('admin.certificates.show', $certificate)->with('status', 'Pengajuan sertifikat ditolak dan alasannya dikirim kepada pemilik akun.');
    }

    public function edit(Certificate $certificate): View
    {
        return view('admin.certificates.form', ['certificate' => $certificate, 'members' => Member::query()->orderBy('full_name')->get(['id', 'full_name', 'nra']), 'activities' => Activity::query()->orderByDesc('planned_at')->get(['id', 'title', 'planned_at']), 'isEditing' => true]);
    }

    public function update(Request $request, Certificate $certificate): RedirectResponse
    {
        $validated = $this->validateCertificate($request, true);
        $oldFile = $certificate->file_path;
        if ($request->hasFile('pdf')) {
            $validated['file_path'] = $request->file('pdf')->store('certificates', 'local');
        }
        $certificate->update($validated);
        if (isset($validated['file_path']) && $oldFile !== $validated['file_path']) {
            Storage::disk('local')->delete($oldFile);
        }

        return redirect()->route('admin.certificates.show', $certificate)->with('status', 'Sertifikat berhasil diperbarui.');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        $filePath = $certificate->file_path;
        $certificate->delete();
        Storage::disk('local')->delete($filePath);

        return redirect()->route('admin.certificates.index')->with('status', 'Sertifikat dan file PDF-nya berhasil dihapus.');
    }

    private function validateCertificate(Request $request, bool $isEditing = false): array
    {
        return $request->validate([
            'member_id' => ['required', 'integer', Rule::exists('members', 'id')],
            'activity_id' => ['required', 'integer', Rule::exists('activities', 'id')],
            'title' => ['required', 'string', 'max:180'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'issued_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'pdf' => [$isEditing ? 'nullable' : 'required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120'],
        ]);
    }
}
