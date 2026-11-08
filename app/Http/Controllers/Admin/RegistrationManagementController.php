<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationApplicant;
use App\Models\RegistrationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RegistrationManagementController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.registration.index', [
            'registration' => RegistrationSetting::publicData(),
            'applicants' => RegistrationApplicant::query()->latest()->paginate(20),
            'routePrefix' => $request->user()->role === 'pengurus' ? 'pengurus' : 'admin',
            'isAdmin' => $request->user()->isAdmin(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_open' => ['required', 'boolean'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date', 'after_or_equal:opens_at'],
            'interview_starts_at' => ['nullable', 'date'],
            'interview_ends_at' => ['nullable', 'date', 'after_or_equal:interview_starts_at'],
            'announcement_at' => ['nullable', 'date', 'after_or_equal:interview_ends_at'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'fee' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'whatsapp_group_link' => ['nullable', 'url', 'max:2048'],
            'pamphlet' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_pamphlet' => ['sometimes', 'boolean'],
        ]);

        $settings = RegistrationSetting::query()->first();

        if ($settings === null) {
            $defaults = config('registration');
            $settings = new RegistrationSetting([
                'is_open' => $defaults['is_open'],
                'opens_at' => $defaults['opens_at'],
                'closes_at' => $defaults['closes_at'],
                'interview_starts_at' => $defaults['interview_starts_at'],
                'interview_ends_at' => $defaults['interview_ends_at'],
                'announcement_at' => $defaults['announcement_at'],
                'quota' => $defaults['quota'],
                'fee' => $defaults['fee'],
                'whatsapp_group_link' => $defaults['whatsapp_group_link'],
                'pamphlet_path' => $defaults['pamphlet_path'],
            ]);
        }

        $oldPamphlet = $settings->pamphlet_path;
        $pamphlet = $request->file('pamphlet');
        $removePamphlet = $request->boolean('remove_pamphlet');
        unset($validated['pamphlet'], $validated['remove_pamphlet']);
        $validated['pamphlet_path'] = $pamphlet?->store('registration', 'public')
            ?? ($removePamphlet ? null : $oldPamphlet);
        $validated['updated_by'] = $request->user()->id;
        $settings->fill($validated)->save();

        if ($oldPamphlet !== null && $oldPamphlet !== $settings->pamphlet_path) {
            Storage::disk('public')->delete($oldPamphlet);
        }

        $route = $request->user()->role === 'pengurus' ? 'pengurus.registration.index' : 'admin.registration.index';
        $status = $settings->is_open ? 'Pendaftaran dibuka dan informasinya diperbarui.' : 'Pendaftaran ditutup dan informasinya diperbarui.';

        return redirect()->route($route)->with('status', $status);
    }

    public function destroyApplicant(Request $request, RegistrationApplicant $applicant): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $applicant->delete();

        return redirect()->route('admin.registration.index')->with('status', 'Data pendaftar dihapus dari sistem.');
    }
}
