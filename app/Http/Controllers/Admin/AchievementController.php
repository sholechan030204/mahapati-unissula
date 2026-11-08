<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Support\AchievementInput;
use App\Support\DashboardNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function __construct(private AchievementInput $achievementInput) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in([Achievement::STATUS_DRAFT, Achievement::STATUS_PENDING, Achievement::STATUS_PUBLISHED, Achievement::STATUS_REJECTED])],
        ]);
        $query = Achievement::query()
            ->with(['createdBy:id,name', 'reviewedBy:id,name'])
            ->when(($filters['q'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $term = '%'.$filters['q'].'%';
                $query->where(function (Builder $achievementQuery) use ($term): void {
                    $achievementQuery->where('title', 'like', $term)
                        ->orWhere('summary', 'like', $term)
                        ->orWhere('level', 'like', $term)
                        ->orWhere('organizer', 'like', $term);
                });
            })
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']));

        return view('admin.achievements.index', [
            'achievements' => $query->latest('achievement_date')->latest()->paginate(12)->withQueryString(),
            'filters' => $filters,
            'publishedCount' => Achievement::query()->where('status', Achievement::STATUS_PUBLISHED)->count(),
            'draftCount' => Achievement::query()->where('status', Achievement::STATUS_DRAFT)->count(),
            'pendingCount' => Achievement::query()->where('status', Achievement::STATUS_PENDING)->count(),
            'rejectedCount' => Achievement::query()->where('status', Achievement::STATUS_REJECTED)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.achievements.form', [
            'achievement' => new Achievement(['status' => Achievement::STATUS_DRAFT]),
            'isEditing' => false,
            'isSubmission' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $status = $request->validate(['status' => ['required', Rule::in([Achievement::STATUS_DRAFT, Achievement::STATUS_PUBLISHED])]])['status'];
        $validated = $this->achievementInput->validateAndStore($request);
        $validated['status'] = $status;
        $validated['created_by'] = $request->user()->id;
        $validated['published_at'] = $status === Achievement::STATUS_PUBLISHED ? now() : null;
        $achievement = Achievement::query()->create($validated);

        return redirect()->route('admin.achievements.show', $achievement)->with('status', 'Prestasi berhasil disimpan.');
    }

    public function show(Achievement $achievement): View
    {
        return view('admin.achievements.show', ['achievement' => $achievement->load(['createdBy', 'reviewedBy'])]);
    }

    public function edit(Achievement $achievement): View
    {
        return view('admin.achievements.form', [
            'achievement' => $achievement,
            'isEditing' => true,
            'isSubmission' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function update(Request $request, Achievement $achievement): RedirectResponse
    {
        $status = $request->validate(['status' => ['required', Rule::in([Achievement::STATUS_DRAFT, Achievement::STATUS_PUBLISHED])]])['status'];
        $validated = $this->achievementInput->validateAndStore($request, $achievement);
        $oldImage = $achievement->image_path;
        $validated['status'] = $status;
        $validated['published_at'] = $status === Achievement::STATUS_PUBLISHED ? ($achievement->published_at ?? now()) : null;
        $validated['rejection_reason'] = null;
        $validated['reviewed_by'] = null;
        $validated['reviewed_at'] = null;
        $achievement->update($validated);

        if ($oldImage !== null && $oldImage !== $achievement->image_path) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.achievements.show', $achievement)->with('status', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $imagePath = $achievement->image_path;
        $achievement->delete();

        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('admin.achievements.index')->with('status', 'Catatan prestasi berhasil dihapus.');
    }

    public function approve(Request $request, Achievement $achievement): RedirectResponse
    {
        abort_unless($achievement->status === Achievement::STATUS_PENDING, 409, 'Kiriman ini sudah pernah diproses.');
        $achievement->update([
            'status' => Achievement::STATUS_PUBLISHED,
            'published_at' => now(),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
        $submissionRoute = $achievement->createdBy?->role === 'pengurus' ? 'pengurus.achievements.index' : 'member.achievement-submissions.index';
        DashboardNotifier::user($achievement->createdBy, 'Prestasi disetujui', '“'.$achievement->title.'” telah disetujui dan diterbitkan.', route($submissionRoute));

        return redirect()->route('admin.achievements.show', $achievement)->with('status', 'Prestasi disetujui dan diterbitkan ke website publik.');
    }

    public function reject(Request $request, Achievement $achievement): RedirectResponse
    {
        abort_unless($achievement->status === Achievement::STATUS_PENDING, 409, 'Kiriman ini sudah pernah diproses.');
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'min:3', 'max:2000']]);
        $achievement->update([
            'status' => Achievement::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'published_at' => null,
        ]);
        $submissionRoute = $achievement->createdBy?->role === 'pengurus' ? 'pengurus.achievements.index' : 'member.achievement-submissions.index';
        DashboardNotifier::user($achievement->createdBy, 'Prestasi perlu diperbaiki', '“'.$achievement->title.'” ditolak. Alasan Admin: '.$validated['rejection_reason'], route($submissionRoute));

        return redirect()->route('admin.achievements.show', $achievement)->with('status', 'Kiriman prestasi ditolak. Alasan sudah dicatat.');
    }
}
