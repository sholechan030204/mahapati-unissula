<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberChangeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const PROFILE_PHOTO_MAX_KILOBYTES = 2048;

    public function edit(Request $request): View
    {
        $user = $request->user()->load('member');

        return view('member.profile', ['user' => $user, 'member' => $user->member]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $member = $user->member;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'alam_name' => [$member === null ? 'prohibited' : 'nullable', 'string', 'max:255'],
            'profile_photo' => [$member === null ? 'prohibited' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::PROFILE_PHOTO_MAX_KILOBYTES],
        ]);

        $newPhotoPath = $request->hasFile('profile_photo')
            ? $request->file('profile_photo')->store('member-photos', 'public')
            : null;
        $oldPhotoPath = $member?->profile_photo;

        DB::transaction(function () use ($user, $member, $validated, $newPhotoPath): void {
            $user->name = $validated['name'];
            $user->save();

            if (! $member instanceof Member) {
                return;
            }

            $changes = [
                'full_name' => $validated['name'],
                'alam_name' => $validated['alam_name'] ?? null,
            ];

            if ($newPhotoPath !== null) {
                $changes['profile_photo'] = $newPhotoPath;
            }

            foreach ($changes as $field => $newValue) {
                $oldValue = $member->{$field};

                if ($oldValue === $newValue) {
                    continue;
                }

                MemberChangeLog::query()->create([
                    'member_id' => $member->id,
                    'changed_by' => $user->id,
                    'field_name' => $field,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);
            }

            $member->update($changes);
        });

        if ($newPhotoPath !== null && $oldPhotoPath !== null && $oldPhotoPath !== $newPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return back()->with('status', 'Profil berhasil diperbarui. Perubahan nama dan nama alam tercatat untuk Admin.');
    }
}
