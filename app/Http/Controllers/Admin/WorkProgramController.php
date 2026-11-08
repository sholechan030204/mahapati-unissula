<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkProgramController extends Controller
{
    public function create(): View
    {
        return view('admin.work-programs.form', [
            'workProgram' => new WorkProgram(['status' => WorkProgram::STATUS_PLANNED]),
            'isEditing' => false,
            'isProposal' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $workProgram = WorkProgram::query()->create($validated + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.work-programs.show', $workProgram)
            ->with('status', 'Program kerja berhasil ditambahkan.');
    }

    public function show(WorkProgram $workProgram): View
    {
        $workProgram->load(['activities' => fn ($query) => $query->withCount('participants')->orderBy('planned_at')]);

        return view('admin.work-programs.show', [
            'workProgram' => $workProgram,
            'isProposal' => false,
            'isReadOnly' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function edit(WorkProgram $workProgram): View
    {
        return view('admin.work-programs.form', [
            'workProgram' => $workProgram,
            'isEditing' => true,
            'isProposal' => false,
        ]);
    }

    public function update(Request $request, WorkProgram $workProgram): RedirectResponse
    {
        $workProgram->update($request->validate($this->rules()));

        return redirect()->route('admin.work-programs.show', $workProgram)
            ->with('status', 'Program kerja berhasil diperbarui.');
    }

    public function destroy(WorkProgram $workProgram): RedirectResponse
    {
        $workProgram->delete();

        return redirect()->route('admin.activities.index', ['tab' => 'programs'])
            ->with('status', 'Program kerja berhasil dihapus.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
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
}
