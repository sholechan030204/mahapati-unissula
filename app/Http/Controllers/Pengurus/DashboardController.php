<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityChangeRequest;
use App\Models\Article;
use App\Models\Achievement;
use App\Models\CashTransaction;
use App\Models\GalleryChangeRequest;
use App\Models\WorkProgram;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $timelineYear = now()->year;
        $daysInYear = Carbon::create($timelineYear, 1, 1)->diffInDays(Carbon::create($timelineYear + 1, 1, 1));
        $timelinePrograms = WorkProgram::query()
            ->whereYear('planned_start_date', $timelineYear)
            ->orderBy('planned_start_date')
            ->get()
            ->map(fn (WorkProgram $program): array => [
                'workProgram' => $program,
                'plannedBar' => $this->timelineBar($program->planned_start_date, $program->planned_end_date, $timelineYear, $daysInYear),
                'actualBar' => $this->timelineBar($program->actual_start_date, $program->actual_end_date, $timelineYear, $daysInYear),
            ]);

        $pendingActivityChanges = ActivityChangeRequest::query()
            ->where('proposed_by', $user->id)
            ->where('status', ActivityChangeRequest::STATUS_PENDING)
            ->count();
        $pendingContent = Article::query()->where('created_by', $user->id)->where('status', Article::STATUS_PENDING)->count()
            + Achievement::query()->where('created_by', $user->id)->where('status', Achievement::STATUS_PENDING)->count()
            + GalleryChangeRequest::query()->where('proposed_by', $user->id)->where('status', GalleryChangeRequest::STATUS_PENDING)->count();

        return view('pengurus.dashboard', [
            'timelineYear' => $timelineYear,
            'timelinePrograms' => $timelinePrograms,
            'activityCount' => Activity::query()->count(),
            'programCount' => WorkProgram::query()->count(),
            'pendingActivityChanges' => $pendingActivityChanges,
            'pendingContent' => $pendingContent,
            'recentActivities' => Activity::query()->with('workProgram')->latest('updated_at')->limit(5)->get(),
            'recentNotifications' => $user->notifications()->latest()->limit(4)->get(),
            'cashBalance' => CashTransaction::query()->where('type', CashTransaction::TYPE_INCOME)->sum('amount')
                - CashTransaction::query()->where('type', CashTransaction::TYPE_EXPENSE)->sum('amount'),
            'cashMonthIncome' => CashTransaction::query()->where('type', CashTransaction::TYPE_INCOME)->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount'),
            'cashMonthExpenses' => CashTransaction::query()->where('type', CashTransaction::TYPE_EXPENSE)->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount'),
        ]);
    }

    /** @return array{left: float, width: float}|null */
    private function timelineBar(?Carbon $start, ?Carbon $end, int $year, float $daysInYear): ?array
    {
        if ($start === null) {
            return null;
        }

        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();
        $periodEnd = ($end ?? $start)->copy()->endOfDay();
        $periodStart = $start->copy()->startOfDay();

        if ($periodEnd->lt($yearStart) || $periodStart->gt($yearEnd)) {
            return null;
        }

        $visibleStart = $periodStart->lt($yearStart) ? $yearStart : $periodStart;
        $visibleEnd = $periodEnd->gt($yearEnd) ? $yearEnd : $periodEnd;
        $left = ($yearStart->diffInDays($visibleStart) / $daysInYear) * 100;
        $duration = $visibleStart->diffInDays($visibleEnd) + 1;

        return ['left' => round($left, 2), 'width' => round(min(100 - $left, max(1.5, ($duration / $daysInYear) * 100)), 2)];
    }
}
