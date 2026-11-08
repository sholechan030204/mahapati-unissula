<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Member;
use App\Models\WorkProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function create(): View
    {
        return $this->activityForm(new Activity(['status' => Activity::STATUS_PLANNED]), false);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $participantIds = $validated['participant_ids'] ?? [];
        $validated['is_featured'] = $request->boolean('is_featured');
        unset($validated['participant_ids']);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('activity-covers', 'public');
        }

        $activity = DB::transaction(function () use ($validated, $participantIds, $request): Activity {
            if ($validated['is_featured']) {
                Activity::query()->where('is_featured', true)->update(['is_featured' => false]);
            }

            $activity = Activity::query()->create($validated + ['created_by' => $request->user()->id]);
            $activity->participants()->sync($participantIds);

            return $activity;
        });

        return redirect()->route('admin.activities.show', $activity)->with('status', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Activity $activity): View
    {
        $activity->load(['workProgram', 'participants']);

        return view('admin.activities.show', [
            'activity' => $activity,
            'isProposal' => false,
            'isReadOnly' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function edit(Activity $activity): View
    {
        $activity->load('participants');

        return $this->activityForm($activity, true);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $participantIds = $validated['participant_ids'] ?? [];
        $validated['is_featured'] = $request->boolean('is_featured');
        unset($validated['participant_ids']);
        $oldCover = $activity->cover_image;

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('activity-covers', 'public');
        } else {
            unset($validated['cover_image']);
        }

        DB::transaction(function () use ($validated, $participantIds, $activity): void {
            if ($validated['is_featured']) {
                Activity::query()->where('is_featured', true)->where('id', '!=', $activity->id)->update(['is_featured' => false]);
            }

            $activity->update($validated);
            $activity->participants()->sync($participantIds);
        });

        if ($request->hasFile('cover_image') && $oldCover !== null) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('admin.activities.show', $activity)->with('status', 'Data kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $cover = $activity->cover_image;
        $activity->delete();

        if ($cover !== null) {
            Storage::disk('public')->delete($cover);
        }

        return redirect()->route('admin.activities.index')->with('status', 'Kegiatan berhasil dihapus.');
    }

    private function activityForm(Activity $activity, bool $isEditing): View
    {
        $selectedMemberIds = $activity->relationLoaded('participants')
            ? $activity->participants->modelKeys()
            : [];

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
            'isProposal' => false,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
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
}
