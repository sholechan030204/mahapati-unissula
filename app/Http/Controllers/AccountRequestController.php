<?php

namespace App\Http\Controllers;

use App\Models\AccountRequest;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountRequestController extends Controller
{
    public function create(): View
    {
        return view('auth.account-request');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'nra' => ['nullable', 'string', 'max:50'],
            'nim' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'whatsapp' => ['required', 'string', 'max:30'],
        ], [
            'email.unique' => 'Email ini sudah memiliki akun. Silakan langsung masuk atau gunakan pemulihan password.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if (blank($request->input('nra')) && blank($request->input('nim'))) {
                $validator->errors()->add('nra', 'Isi minimal salah satu: NRA atau NIM agar Admin dapat mencocokkan data anggota.');
            }

            $hasPendingRequest = AccountRequest::query()
                ->where('email', $request->input('email'))
                ->where('status', AccountRequest::STATUS_PENDING)
                ->exists();

            if ($hasPendingRequest) {
                $validator->errors()->add('email', 'Pengajuan dengan email ini masih menunggu pemeriksaan Admin.');
            }
        });

        $validated = $validator->validate();
        $validated['status'] = AccountRequest::STATUS_PENDING;

        AccountRequest::query()->create($validated);
        DashboardNotifier::admins('Pengajuan akun anggota baru', $validated['name'].' mengajukan akun MAHAPATI.', route('admin.account-requests.index'));

        return redirect()->route('account-request.create')->with(
            'status',
            'Pengajuan akun berhasil dikirim. Admin akan memeriksa data dan menentukan role akun.',
        );
    }
}
