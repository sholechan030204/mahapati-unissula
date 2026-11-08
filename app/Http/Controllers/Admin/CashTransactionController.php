<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $allTimeIncome = CashTransaction::query()->where('type', CashTransaction::TYPE_INCOME)->sum('amount');
        $allTimeExpenses = CashTransaction::query()->where('type', CashTransaction::TYPE_EXPENSE)->sum('amount');
        $filteredQuery = $this->applyFilters(CashTransaction::query(), $filters);
        $transactions = (clone $filteredQuery)
            ->with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.cash.index', [
            'transactions' => $transactions,
            'currentBalance' => $allTimeIncome - $allTimeExpenses,
            'periodIncome' => (clone $filteredQuery)->where('type', CashTransaction::TYPE_INCOME)->sum('amount'),
            'periodExpenses' => (clone $filteredQuery)->where('type', CashTransaction::TYPE_EXPENSE)->sum('amount'),
            'filters' => $filters,
            'monthlyDuesSpreadsheetUrl' => config('cash.monthly_dues_spreadsheet_url'),
        ]);
    }

    public function create(): View
    {
        return view('admin.cash.form', [
            'transaction' => new CashTransaction([
                'transaction_date' => today(),
                'type' => CashTransaction::TYPE_INCOME,
            ]),
            'isEditing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['created_by'] = $request->user()->id;
        CashTransaction::query()->create($validated);

        return redirect()->route('admin.cash-transactions.index')->with('status', 'Transaksi kas berhasil dicatat.');
    }

    public function show(CashTransaction $cashTransaction): View
    {
        return view('admin.cash.show', [
            'transaction' => $cashTransaction->load(['createdBy:id,name', 'updatedBy:id,name']),
        ]);
    }

    public function edit(CashTransaction $cashTransaction): View
    {
        return view('admin.cash.form', [
            'transaction' => $cashTransaction,
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, CashTransaction $cashTransaction): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['updated_by'] = $request->user()->id;
        $cashTransaction->update($validated);

        return redirect()->route('admin.cash-transactions.index')->with('status', 'Transaksi kas berhasil diperbarui.');
    }

    public function destroy(CashTransaction $cashTransaction): RedirectResponse
    {
        $cashTransaction->delete();

        return redirect()->route('admin.cash-transactions.index')->with('status', 'Transaksi kas berhasil dihapus.');
    }

    /**
     * @param array{q?: string|null, date_from?: string|null, date_to?: string|null, month?: int|string|null, year?: int|string|null} $filters
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(($filters['q'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $query->where('description', 'like', '%'.$filters['q'].'%');
            })
            ->when(! empty($filters['date_from']), fn (Builder $query): Builder => $query->whereDate('transaction_date', '>=', $filters['date_from']))
            ->when(! empty($filters['date_to']), fn (Builder $query): Builder => $query->whereDate('transaction_date', '<=', $filters['date_to']))
            ->when(! empty($filters['month']), fn (Builder $query): Builder => $query->whereMonth('transaction_date', $filters['month']))
            ->when(! empty($filters['year']), fn (Builder $query): Builder => $query->whereYear('transaction_date', $filters['year']));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999999999'],
            'description' => ['required', 'string', 'max:2000'],
        ];
    }
}
