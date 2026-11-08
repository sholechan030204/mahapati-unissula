<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load('member');

        return view('member.account', ['user' => $user, 'member' => $user->member]);
    }

    public function edit(Request $request): View
    {
        return view('member.account-settings', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $emailIsChanging = mb_strtolower((string) $request->input('email')) !== mb_strtolower($user->email);
        $passwordIsChanging = filled($request->input('password'));

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => [
                'nullable',
                new RequiredIf($emailIsChanging || $passwordIsChanging),
                'current_password',
            ],
            'password' => ['nullable', 'confirmed', 'min:12'],
        ]);

        $user->email = $validated['email'];

        if (filled($validated['password'] ?? null)) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'Pengaturan akun berhasil diperbarui.');
    }
}
