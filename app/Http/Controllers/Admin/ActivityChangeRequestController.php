<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityChangeRequest;
use App\Models\WorkProgram;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ActivityChangeRequestController extends Controller
{
    public function index(): View
    {
        $requests = ActivityChangeRequest::query()
            ->with(['proposedBy', 'reviewedBy'])
            ->latest()
            ->paginate(10);

        return view('admin.activity-requests.index', ['requests' => $requests]);
    }

    public function approve(Request $request, ActivityChangeRequest $activityChangeRequest): RedirectResponse
    {
        abort_unless($activityChangeRequest->status === ActivityChangeRequest::STATUS_PENDING, 409, 'Usulan ini sudah pernah diproses.');

        $oldCover = null;
        $newSubjectId = $activityChangeRequest->subject_id;

        DB::transaction(function () use ($request, $activityChangeRequest, &$oldCover, &$newSubjectId): void {
            $payload = $activityChangeRequest->payload ?? [];

            if ($activityChangeRequest->subject_type === ActivityChangeRequest::TYPE_PROGRAM) {
                $newSubjectId = $this->applyProgramChange($activityChangeRequest, $payload);
            } elseif ($activityChangeRequest->subject_type === ActivityChangeRequest::TYPE_ACTIVITY) {
                [$newSubjectId, $oldCover] = $this->applyActivityChange($activityChangeRequest, $payload);
            } else {
                abort(422, 'Jenis usulan tidak dikenali.');
            }

            $activityChangeRequest->update([
                'subject_id' => $newSubjectId,
                'status' => ActivityChangeRequest::STATUS_APPROVED,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        if ($oldCover !== null) {
            Storage::disk('public')->delete($oldCover);
        }
        DashboardNotifier::user($activityChangeRequest->proposedBy, 'Usulan kegiatan disetujui', 'Usulan '.$this->subjectLabel($activityChangeRequest).' Anda telah disetujui oleh Admin.', route('pengurus.activities.index'));

        return redirect()->route('admin.activity-requests.index')->with('status', 'Usulan disetujui dan perubahan diterapkan.');
    }

    public function reject(Request $request, ActivityChangeRequest $activityChangeRequest): RedirectResponse
    {
        abort_unless($activityChangeRequest->status === ActivityChangeRequest::STATUS_PENDING, 409, 'Usulan ini sudah pernah diproses.');

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $this->removeUnapprovedCover($activityChangeRequest);
        $activityChangeRequest->update([
            'status' => ActivityChangeRequest::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        DashboardNotifier::user($activityChangeRequest->proposedBy, 'Usulan kegiatan ditolak', 'Usulan '.$this->subjectLabel($activityChangeRequest).' Anda ditolak. Alasan Admin: '.$validated['rejection_reason'], route('pengurus.activities.index'));

        return redirect()->route('admin.activity-requests.index')->with('status', 'Usulan ditolak. Alasan penolakan sudah tercatat.');
    }

    /** @param array<string, mixed> $payload */
    private function applyProgramChange(ActivityChangeRequest $changeRequest, array $payload): ?int
    {
        if ($changeRequest->action === ActivityChangeRequest::ACTION_CREATE) {
            return WorkProgram::query()->create($payload + ['created_by' => $changeRequest->proposed_by])->id;
        }

        $workProgram = WorkProgram::query()->findOrFail($changeRequest->subject_id);

        if ($changeRequest->action === ActivityChangeRequest::ACTION_UPDATE) {
            $workProgram->update($payload);

            return $workProgram->id;
        }

        if ($changeRequest->action === ActivityChangeRequest::ACTION_DELETE) {
            $workProgram->delete();

            return null;
        }

        abort(422, 'Aksi usulan program kerja tidak dikenali.');
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{0: int|null, 1: string|null}
     */
    private function applyActivityChange(ActivityChangeRequest $changeRequest, array $payload): array
    {
        $participantIds = $payload['participant_ids'] ?? [];
        unset($payload['participant_ids']);

        if ($changeRequest->action === ActivityChangeRequest::ACTION_CREATE) {
            if (($payload['is_featured'] ?? false) === true) {
                Activity::query()->where('is_featured', true)->update(['is_featured' => false]);
            }

            $activity = Activity::query()->create($payload + ['created_by' => $changeRequest->proposed_by]);
            $activity->participants()->sync($participantIds);

            return [$activity->id, null];
        }

        $activity = Activity::query()->findOrFail($changeRequest->subject_id);

        if ($changeRequest->action === ActivityChangeRequest::ACTION_UPDATE) {
            $oldCover = $activity->cover_image;

            if (($payload['is_featured'] ?? false) === true) {
                Activity::query()->where('is_featured', true)->where('id', '!=', $activity->id)->update(['is_featured' => false]);
            }

            $activity->update($payload);
            $activity->participants()->sync($participantIds);
            $coverWasReplaced = isset($payload['cover_image']) && $payload['cover_image'] !== $oldCover;

            return [$activity->id, $coverWasReplaced ? $oldCover : null];
        }

        if ($changeRequest->action === ActivityChangeRequest::ACTION_DELETE) {
            $oldCover = $activity->cover_image;
            $activity->delete();

            return [null, $oldCover];
        }

        abort(422, 'Aksi usulan kegiatan tidak dikenali.');
    }

    private function removeUnapprovedCover(ActivityChangeRequest $changeRequest): void
    {
        if ($changeRequest->subject_type !== ActivityChangeRequest::TYPE_ACTIVITY) {
            return;
        }

        $proposedCover = $changeRequest->payload['cover_image'] ?? null;

        if ($proposedCover === null) {
            return;
        }

        $currentCover = $changeRequest->subject_id !== null
            ? Activity::query()->whereKey($changeRequest->subject_id)->value('cover_image')
            : null;

        if ($proposedCover !== $currentCover) {
            Storage::disk('public')->delete($proposedCover);
        }
    }

    private function subjectLabel(ActivityChangeRequest $changeRequest): string
    {
        $title = $changeRequest->payload['title'] ?? null;
        if (! is_string($title) && $changeRequest->subject_type === ActivityChangeRequest::TYPE_ACTIVITY) {
            $title = Activity::query()->whereKey($changeRequest->subject_id)->value('title');
        }
        if (! is_string($title) && $changeRequest->subject_type === ActivityChangeRequest::TYPE_PROGRAM) {
            $title = WorkProgram::query()->whereKey($changeRequest->subject_id)->value('title');
        }

        return $title ?: 'konten';
    }
}
