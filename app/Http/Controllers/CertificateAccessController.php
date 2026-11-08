<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateAccessController extends Controller
{
    public function mine(Request $request): View
    {
        $member = $request->user()->member;
        $certificates = $member ? $member->certificates()->with('activity:id,title,planned_at')->latest('issued_at')->latest()->paginate(12) : Certificate::query()->whereRaw('1 = 0')->paginate(12);

        return view('certificates.mine', ['certificates' => $certificates, 'hasMemberProfile' => $member !== null]);
    }

    public function download(Request $request, Certificate $certificate): StreamedResponse
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';
        abort_unless($isAdmin || ($user->member?->id === $certificate->member_id && $certificate->status === Certificate::STATUS_APPROVED), 403);
        abort_unless(Storage::disk('local')->exists($certificate->file_path), 404);
        $filename = str($certificate->title)->slug('-')->limit(80, '')->append('.pdf')->toString();

        return Storage::disk('local')->download($certificate->file_path, $filename, ['Content-Type' => 'application/pdf']);
    }
}
