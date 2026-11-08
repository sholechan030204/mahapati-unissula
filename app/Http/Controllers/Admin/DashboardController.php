<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use App\Models\ActivityChangeRequest;
use App\Models\CashTransaction;
use App\Models\Member;
use App\Models\MemberChangeLog;
use App\Models\WorkProgram;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $timelineYear = now()->year;
        $daysInYear = Carbon::create($timelineYear, 1, 1)->diffInDays(Carbon::create($timelineYear + 1, 1, 1));
        $timelinePrograms = WorkProgram::query()
            ->whereYear('planned_start_date', $timelineYear)
            ->orderBy('planned_start_date')
            ->limit(8)
            ->get()
            ->map(function (WorkProgram $workProgram) use ($timelineYear, $daysInYear): array {
                return [
                    'workProgram' => $workProgram,
                    'plannedBar' => $this->timelineBar($workProgram->planned_start_date, $workProgram->planned_end_date, $timelineYear, $daysInYear),
                    'actualBar' => $this->timelineBar($workProgram->actual_start_date, $workProgram->actual_end_date, $timelineYear, $daysInYear),
                ];
            });

        return view('admin.dashboard', [
            'totalMembers' => Member::query()->count(),
            'activeMembers' => Member::query()->where('status', Member::STATUS_ACTIVE)->count(),
            'permanentMembers' => Member::query()->where('membership_type', Member::TYPE_PERMANENT)->count(),
            'specialMembers' => Member::query()->where('membership_type', Member::TYPE_SPECIAL)->count(),
            'pendingAccountRequests' => AccountRequest::query()->where('status', AccountRequest::STATUS_PENDING)->count(),
            'pendingActivityRequests' => ActivityChangeRequest::query()->where('status', ActivityChangeRequest::STATUS_PENDING)->count(),
            'timelineYear' => $timelineYear,
            'timelinePrograms' => $timelinePrograms,
            'recentAccountRequests' => AccountRequest::query()->latest()->limit(4)->get(),
            'recentMemberChanges' => MemberChangeLog::query()->with(['member', 'changedBy'])->latest()->limit(4)->get(),
            'cashBalance' => CashTransaction::query()->where('type', CashTransaction::TYPE_INCOME)->sum('amount')
                - CashTransaction::query()->where('type', CashTransaction::TYPE_EXPENSE)->sum('amount'),
            'cashMonthIncome' => CashTransaction::query()->where('type', CashTransaction::TYPE_INCOME)->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount'),
            'cashMonthExpenses' => CashTransaction::query()->where('type', CashTransaction::TYPE_EXPENSE)->whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('amount'),
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
