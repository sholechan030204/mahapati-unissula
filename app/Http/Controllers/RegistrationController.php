<?php

namespace App\Http\Controllers;

use App\Models\RegistrationApplicant;
use App\Models\RegistrationSetting;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function show(): View
    {
        return view('public.registration', ['registration' => RegistrationSetting::publicData()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $registration = RegistrationSetting::publicData();

        if (! $registration['is_open']) {
            return redirect()->route('registration')->withErrors(['registration' => 'Pendaftaran sedang ditutup.']);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:180'],
            'student_number' => ['required', 'string', 'max:40'],
            'study_program' => ['required', 'string', 'max:150'],
            'cohort' => ['required', 'digits:4'],
            'semester' => ['required', 'integer', 'in:1,2,3'],
            'email' => ['required', 'email', 'max:190'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'terms_accepted' => ['accepted'],
            'eligibility_confirmed' => ['accepted'],
        ]);

        unset($validated['terms_accepted'], $validated['eligibility_confirmed']);
        RegistrationApplicant::query()->create($validated);
        DashboardNotifier::admins('Pendaftar baru', $validated['full_name'].' mengirim formulir awal open recruitment.', route('admin.registration.index'));

        return redirect()->route('registration')->with([
            'registrationSubmitted' => true,
            'whatsappGroupLink' => $registration['whatsapp_group_link'],
        ]);
    }
}
