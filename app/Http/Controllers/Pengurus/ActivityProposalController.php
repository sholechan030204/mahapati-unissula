<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityChangeRequest;
use App\Models\Member;
use App\Models\WorkProgram;
use App\Support\DashboardNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivityProposalController extends Controller
{
    public function createProgram(): View
    {
        return $this->programForm(new WorkProgram(['status' => WorkProgram::STATUS_PLANNED]), false);
    }

    public function storeProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->programRules());
        $this->createRequest(ActivityChangeRequest::TYPE_PROGRAM, ActivityChangeRequest::ACTION_CREATE, null, $validated, $request);

        return $this->backToList('programs', 'Usulan program kerja dikirim kepada Admin untuk disetujui.');
    }

    public function editProgram(WorkProgram $workProgram): View
    {
        $this->ensureNoPendingRequest(ActivityChangeRequest::TYPE_PROGRAM, $workProgram->id);

        return $this->programForm($workProgram, true);
    }

    public function updateProgram(Request $request, WorkProgram $workProgram): RedirectResponse
    {
        $this->ensureNoPendingRequest(ActivityChangeRequest::TYPE_PROGRAM, $workProgram->id);
        $validated = $request->validate($this->programRules());
        $this->createRequest(ActivityChangeRequest::TYPE_PROGRAM, ActivityChangeRequest::ACTION_UPDATE, $workProgram->id, $validated, $request);

        return $this->backToList('programs', 'Usulan perubahan program kerja dikirim kepada Admin untuk disetujui.');
    }

    public function destroyProgram(Request $request, WorkProgram $workProgram): RedirectResponse
    {
        $this->ensureNoPendingRequest(ActivityChangeRequest::TYPE_PROGRAM, $workProgram->id);
        $this->createRequest(ActivityChangeRequest::TYPE_PROGRAM, ActivityChangeRequest::ACTION_DELETE, $workProgram->id, null, $request);

        return $this->backToList('programs', 'Usulan penghapusan program kerja dikirim kepada Admin.');
    }

    public function createActivity(): View
    {
        return $this->activityForm(new Activity(['status' => Activity::STATUS_PLANNED]), false);
    }

    public function storeActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->activityRules());
        $validated['is_featured'] = $request->boolean('is_featured');
        $this->storeProposedCover($request, $validated);
        $this->createRequest(ActivityChangeRequest::TYPE_ACTIVITY, ActivityChangeRequest::ACTION_CREATE, null, $validated, $request);

        return $this->backToList('activities', 'Usulan kegiatan dikirim kepada Admin untuk disetujui.');
    }

    public function editActivity(Activity $activity): View
    {
        $this->ensureNoPendingRequest(ActivityChangeRequest::TYPE_ACTIVITY, $activity->id);
        $activity->load('participants');

        return $this->activityForm($activity, true);
    }

    public function updateActivity(Request $request, Activity $activity): RedirectResponse
    {
        $this->ensureNoPendingRequest(ActivityChangeRequest::TYPE_ACTIVITY, $activity->id);
        $validated = $request->validate($this->activityRules());
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['cover_image'] = $activity->cover_image;
        $this->storeProposedCover($request, $validated);
        $this->createRequest(ActivityChangeRequest::TYPE_ACTIVITY, ActivityChangeRequest::ACTION_UPDATE, $activity->id, $validated, $request);

        return $this->backToList('activities', 'Usulan perubahan kegiatan dikirim kepada Admin untuk disetujui.');
    }

    public function destroyActivity(Request $request, Activity $activity): RedirectResponse
    {
        $this->ensureNoPendingRequest(ActivityChangeRequest::TYPE_ACTIVITY, $activity->id);
        $this->createRequest(ActivityChangeRequest::TYPE_ACTIVITY, ActivityChangeRequest::ACTION_DELETE, $activity->id, null, $request);

        return $this->backToList('activities', 'Usulan penghapusan kegiatan dikirim kepada Admin.');
    }

    private function programForm(WorkProgram $workProgram, bool $isEditing): View
    {
        return view('admin.work-programs.form', [
            'workProgram' => $workProgram,
            'isEditing' => $isEditing,
            'isProposal' => true,
        ]);
    }

    private function activityForm(Activity $activity, bool $isEditing): View
    {
        $selectedMemberIds = $activity->relationLoaded('participants') ? $activity->participants->modelKeys() : [];
        $members = Member::query()
            ->where(function (Builder $query) use ($selectedMemberIds): void {
                $query->where('status', Member::STATUS_ACTIVE);

                if ($selectedMemberIds !== []) {
                    $query->orWhereIn('id', $selectedMemberIds);
                }
            })
            ->orderBy('full_name')
            ->get();

        return view('admin.activities.form', [
            'activity' => $activity,
            'members' => $members,
            'workPrograms' => WorkProgram::query()->orderByDesc('planned_start_date')->get(),
            'isEditing' => $isEditing,
            'isProposal' => true,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function programRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'planned_start_date' => ['required', 'date'],
            'planned_end_date' => ['nullable', 'date', 'after_or_equal:planned_start_date'],
            'actual_start_date' => ['required_if:status,completed', 'nullable', 'date'],
            'actual_end_date' => ['nullable', 'date', 'after_or_equal:actual_start_date'],
            'status' => ['required', Rule::in([WorkProgram::STATUS_PLANNED, WorkProgram::STATUS_ONGOING, WorkProgram::STATUS_COMPLETED])],
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function activityRules(): array
    {
        return [
            'work_program_id' => ['nullable', 'integer', 'exists:work_programs,id'],
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'planned_at' => ['required', 'date'],
            'realized_at' => ['required_if:status,completed', 'nullable', 'date'],
            'status' => ['required', Rule::in([Activity::STATUS_PLANNED, Activity::STATUS_ONGOING, Activity::STATUS_COMPLETED])],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'participant_ids' => ['nullable', 'array'],
            'participant_ids.*' => ['integer', 'distinct', 'exists:members,id'],
        ];
    }

    /** @param array<string, mixed>|null $payload */
    private function createRequest(string $subjectType, string $action, ?int $subjectId, ?array $payload, Request $request): ActivityChangeRequest
    {
        $changeRequest = ActivityChangeRequest::query()->create([
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'action' => $action,
            'payload' => $payload,
            'status' => ActivityChangeRequest::STATUS_PENDING,
            'proposed_by' => $request->user()->id,
        ]);
        $subjectLabel = $subjectType === ActivityChangeRequest::TYPE_PROGRAM ? 'program kerja' : 'kegiatan';
        DashboardNotifier::admins('Usulan '.ucfirst($subjectLabel).' baru', $request->user()->name.' mengirim usulan '.$subjectLabel.' untuk ditinjau.', route('admin.activity-requests.index'));

        return $changeRequest;
    }

    /** @param array<string, mixed> $validated */
    private function storeProposedCover(Request $request, array &$validated): void
    {
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('activity-covers', 'public');
        } else {
            unset($validated['cover_image']);
        }
    }

    private function ensureNoPendingRequest(string $subjectType, int $subjectId): void
    {
        abort_if(
            ActivityChangeRequest::query()
                ->where('subject_type', $subjectType)
                ->where('subject_id', $subjectId)
                ->where('status', ActivityChangeRequest::STATUS_PENDING)
                ->exists(),
            409,
            'Masih ada perubahan untuk item ini yang menunggu persetujuan Admin.',
        );
    }

    private function backToList(string $tab, string $message): RedirectResponse
    {
        return redirect()->route('pengurus.activities.index', ['tab' => $tab])->with('status', $message);
    }
}
