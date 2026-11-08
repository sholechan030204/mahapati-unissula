<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityChangeRequest;
use App\Models\WorkProgram;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityManagementController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->user()->role;
        $tab = $request->query('tab', 'programs');

        if (! in_array($tab, ['programs', 'activities', 'approvals'], true)) {
            $tab = 'programs';
        }

        abort_if($tab === 'approvals' && $role !== 'admin', 403);

        $searchTerm = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $workPrograms = WorkProgram::query()
            ->select([
                'id',
                'title',
                'planned_start_date',
                'planned_end_date',
                'actual_start_date',
                'actual_end_date',
                'status',
            ])
            ->selectRaw('LEFT(description, 500) as description')
            ->withCount('activities')
            ->when($searchTerm !== '', function (Builder $query) use ($searchTerm): void {
                $query->where('title', 'like', "%{$searchTerm}%");
            })
            ->when(in_array($status, ['planned', 'ongoing', 'completed'], true), function (Builder $query) use ($status): void {
                $query->where('status', $status);
            })
            ->orderByDesc('planned_start_date')
            ->paginate(12, ['*'], 'program_page')
            ->withQueryString();

        $activities = Activity::query()
            ->select([
                'id',
                'work_program_id',
                'title',
                'location',
                'planned_at',
                'realized_at',
                'status',
            ])
            ->with('workProgram:id,title')
            ->withCount('participants')
            ->when($searchTerm !== '', function (Builder $query) use ($searchTerm): void {
                $query->where(function (Builder $activityQuery) use ($searchTerm): void {
                    $activityQuery->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('location', 'like', "%{$searchTerm}%");
                });
            })
            ->when(in_array($status, ['planned', 'ongoing', 'completed'], true), function (Builder $query) use ($status): void {
                $query->where('status', $status);
            })
            ->orderByDesc('planned_at')
            ->paginate(12, ['*'], 'activity_page')
            ->withQueryString();

        return view('admin.activities.index', [
            'tab' => $tab,
            'workPrograms' => $workPrograms,
            'activities' => $activities,
            'isAdmin' => $role === 'admin',
            'isPengurus' => $role === 'pengurus',
            'isReadOnly' => $role === 'anggota',
            'programCount' => WorkProgram::query()->count(),
            'activityCount' => Activity::query()->count(),
            'completedProgramCount' => WorkProgram::query()->where('status', WorkProgram::STATUS_COMPLETED)->count(),
            'pendingRequestCount' => $role === 'admin'
                ? ActivityChangeRequest::query()->where('status', ActivityChangeRequest::STATUS_PENDING)->count()
                : 0,
        ]);
    }

    public function showProgram(Request $request, WorkProgram $workProgram): View
    {
        $workProgram->load(['activities' => fn ($query) => $query->withCount('participants')->orderBy('planned_at')]);

        return view('admin.work-programs.show', [
            'workProgram' => $workProgram,
            'isProposal' => $request->user()->role === 'pengurus',
            'isReadOnly' => $request->user()->role === 'anggota',
            'routePrefix' => $request->user()->role === 'admin' ? 'admin' : ($request->user()->role === 'pengurus' ? 'pengurus' : 'member'),
        ]);
    }

    public function showActivity(Request $request, Activity $activity): View
    {
        $activity->load(['workProgram', 'participants']);
        $role = $request->user()->role;

        return view('admin.activities.show', [
            'activity' => $activity,
            'isProposal' => $role === 'pengurus',
            'isReadOnly' => $role === 'anggota',
            'routePrefix' => $role === 'admin' ? 'admin' : ($role === 'pengurus' ? 'pengurus' : 'member'),
        ]);
    }
}
