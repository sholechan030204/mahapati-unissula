<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Article;
use App\Models\CashTransaction;
use App\Models\Certificate;
use App\Models\GalleryChangeRequest;
use App\Models\Member;
use App\Models\WorkProgram;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();
        $member = $user->member;
        $timelineYear = now()->year;
        $daysInYear = Carbon::create($timelineYear, 1, 1)->diffInDays(Carbon::create($timelineYear + 1, 1, 1));
        $timelinePrograms = WorkProgram::query()
            ->whereYear('planned_start_date', $timelineYear)
            ->orderBy('planned_start_date')
            ->limit(8)
            ->get()
            ->map(fn (WorkProgram $program): array => [
                'workProgram' => $program,
                'plannedBar' => $this->timelineBar($program->planned_start_date, $program->planned_end_date, $timelineYear, $daysInYear),
                'actualBar' => $this->timelineBar($program->actual_start_date, $program->actual_end_date, $timelineYear, $daysInYear),
            ]);

        $cashBalance = CashTransaction::query()->where('type', CashTransaction::TYPE_INCOME)->sum('amount')
            - CashTransaction::query()->where('type', CashTransaction::TYPE_EXPENSE)->sum('amount');
        $cashMonthIncome = CashTransaction::query()
            ->where('type', CashTransaction::TYPE_INCOME)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $cashMonthExpenses = CashTransaction::query()
            ->where('type', CashTransaction::TYPE_EXPENSE)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $activities = $member
            ? $member->activities()->with('workProgram')->orderByDesc('planned_at')->limit(4)->get()
            : collect();
        $recentNotifications = $user->notifications()->latest()->limit(4)->get();
        $achievementCount = Achievement::query()->where('created_by', $user->id)->count();
        $certificateCount = $member?->certificates()->where('status', Certificate::STATUS_APPROVED)->count() ?? 0;
        $contentCount = Article::query()->where('created_by', $user->id)->count()
            + GalleryChangeRequest::query()->where('proposed_by', $user->id)->count()
            + $achievementCount;

        return view('member.dashboard', [
            'member' => $member,
            'hasMemberProfile' => $member instanceof Member,
            'activities' => $activities,
            'activityCount' => $member?->activities()->count() ?? 0,
            'achievementCount' => $achievementCount,
            'certificateCount' => $certificateCount,
            'contentCount' => $contentCount,
            'recentNotifications' => $recentNotifications,
            'timelineYear' => $timelineYear,
            'timelinePrograms' => $timelinePrograms,
            'cashBalance' => $cashBalance,
            'cashMonthIncome' => $cashMonthIncome,
            'cashMonthExpenses' => $cashMonthExpenses,
        ]);
    }

    /**
     * @return array{left: float, width: float}|null
     */
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
        $width = min(100 - $left, max(1.5, ($duration / $daysInYear) * 100));

        return ['left' => round($left, 2), 'width' => round($width, 2)];
    }
}
