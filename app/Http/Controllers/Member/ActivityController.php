<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\WorkProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'programs');

        if (! in_array($tab, ['programs', 'activities'], true)) {
            $tab = 'programs';
        }

        $searchTerm = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        return view('admin.activities.index', [
            'tab' => $tab,
            'workPrograms' => WorkProgram::query()
                ->select(['id', 'title', 'planned_start_date', 'planned_end_date', 'actual_start_date', 'actual_end_date', 'status'])
                ->selectRaw('LEFT(description, 500) as description')
                ->withCount('activities')
                ->when($searchTerm !== '', fn (Builder $query) => $query->where('title', 'like', "%{$searchTerm}%"))
                ->when(in_array($status, ['planned', 'ongoing', 'completed'], true), fn (Builder $query) => $query->where('status', $status))
                ->orderByDesc('planned_start_date')
                ->paginate(12, ['*'], 'program_page')
                ->withQueryString(),
            'activities' => Activity::query()
                ->select(['id', 'work_program_id', 'title', 'location', 'planned_at', 'realized_at', 'status'])
                ->with('workProgram:id,title')
                ->withCount('participants')
                ->when($searchTerm !== '', function (Builder $query) use ($searchTerm): void {
                    $query->where(function (Builder $activityQuery) use ($searchTerm): void {
                        $activityQuery->where('title', 'like', "%{$searchTerm}%")
                            ->orWhere('location', 'like', "%{$searchTerm}%");
                    });
                })
                ->when(in_array($status, ['planned', 'ongoing', 'completed'], true), fn (Builder $query) => $query->where('status', $status))
                ->orderByDesc('planned_at')
                ->paginate(12, ['*'], 'activity_page')
                ->withQueryString(),
            'isAdmin' => false,
            'isPengurus' => false,
            'isReadOnly' => true,
            'programCount' => WorkProgram::query()->count(),
            'activityCount' => Activity::query()->count(),
            'completedProgramCount' => WorkProgram::query()->where('status', WorkProgram::STATUS_COMPLETED)->count(),
            'pendingRequestCount' => 0,
        ]);
    }

    public function showProgram(WorkProgram $workProgram): View
    {
        $workProgram->load(['activities' => fn ($query) => $query->withCount('participants')->orderBy('planned_at')]);

        return view('admin.work-programs.show', [
            'workProgram' => $workProgram,
            'isProposal' => false,
            'isReadOnly' => true,
            'routePrefix' => 'member',
        ]);
    }

    public function showActivity(Activity $activity): View
    {
        $activity->load(['workProgram', 'participants']);

        return view('admin.activities.show', [
            'activity' => $activity,
            'isProposal' => false,
            'isReadOnly' => true,
            'routePrefix' => 'member',
        ]);
    }
}
