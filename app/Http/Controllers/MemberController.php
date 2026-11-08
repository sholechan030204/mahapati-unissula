<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberChangeLog;
use App\Models\User;
use App\Support\DashboardNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    private const PROFILE_PHOTO_MAX_KILOBYTES = 2048;

    private const AUDITED_FIELDS = [
        'full_name' => 'Nama lengkap',
        'alam_name' => 'Nama alam',
        'nra' => 'NRA',
        'nim' => 'NIM',
        'membership_type' => 'Jenis keanggotaan',
        'status' => 'Status keanggotaan',
        'position' => 'Jabatan',
        'profile_photo' => 'Foto profil',
        'address' => 'Alamat lengkap',
        'phone' => 'Nomor telepon',
    ];

    public function index(Request $request): View
    {
        $this->ensureCanViewMembers($request);

        $members = Member::query()
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->string('q'));
                $query->where(function (Builder $memberQuery) use ($term): void {
                    $memberQuery->where('full_name', 'like', "%{$term}%")
                        ->orWhere('nra', 'like', "%{$term}%")
                        ->orWhere('nim', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('membership_type'), fn (Builder $query) => $query->where('membership_type', $request->string('membership_type')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'isReadOnly' => $request->user()->role === 'pengurus',
        ]);
    }

    public function create(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('admin.members.form', [
            'member' => new Member([
                'membership_type' => Member::TYPE_PERMANENT,
                'status' => Member::STATUS_ACTIVE,
            ]),
            'isEditing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate($this->rules());
        $validated['profile_photo'] = $request->file('profile_photo')->store('member-photos', 'public');

        $member = DB::transaction(function () use ($request, $validated): Member {
            $member = Member::query()->create($validated);
            $this->recordChanges($member, $request->user()->id, [], $member->only(array_keys(self::AUDITED_FIELDS)));

            return $member;
        });

        return redirect()->route('admin.members.show', $member)->with('status', 'Data anggota berhasil ditambahkan.');
    }

    public function show(Request $request, Member $member): View
    {
        $this->ensureCanViewMembers($request);

        $member->load(['user', 'changes.changedBy']);

        return view('admin.members.show', [
            'member' => $member,
            'isReadOnly' => $request->user()->role === 'pengurus',
            'fieldLabels' => [...self::AUDITED_FIELDS, 'account_role' => 'Role akun'],
        ]);
    }

    public function edit(Request $request, Member $member): View
    {
        $this->ensureAdmin($request);

        return view('admin.members.form', [
            'member' => $member,
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate($this->rules($member));
        $oldValues = $member->only(array_keys(self::AUDITED_FIELDS));

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('member-photos', 'public');
        } else {
            unset($validated['profile_photo']);
        }

        DB::transaction(function () use ($member, $request, $validated, $oldValues): void {
            $member->update($validated);
            $this->recordChanges($member, $request->user()->id, $oldValues, $member->only(array_keys(self::AUDITED_FIELDS)));
        });

        return redirect()->route('admin.members.show', $member)->with('status', 'Data anggota berhasil diperbarui. Riwayat perubahan tercatat.');
    }

    public function updateRole(Request $request, Member $member): RedirectResponse
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['anggota', 'pengurus'])],
        ]);

        $result = DB::transaction(function () use ($request, $member, $validated): array {
            $lockedMember = Member::query()->whereKey($member->getKey())->lockForUpdate()->firstOrFail();
            $user = User::query()->whereKey($lockedMember->user_id)->lockForUpdate()->first();

            if ($user === null) {
                return ['error' => 'Anggota ini belum terhubung ke akun. Setujui pengajuan akun terlebih dahulu.'];
            }

            if (! in_array($user->role, ['anggota', 'pengurus'], true)) {
                return ['error' => 'Role akun ini tidak dapat diubah melalui Manajemen Anggota.'];
            }

            $oldRole = $user->role;
            $newRole = $validated['role'];

            if ($oldRole === $newRole) {
                return ['user' => $user, 'changed' => false];
            }

            $user->role = $newRole;
            $user->save();

            MemberChangeLog::query()->create([
                'member_id' => $lockedMember->id,
                'changed_by' => $request->user()->id,
                'field_name' => 'account_role',
                'old_value' => $oldRole,
                'new_value' => $newRole,
            ]);

            return ['user' => $user, 'changed' => true];
        });

        if (isset($result['error'])) {
            return back()->withErrors(['role' => $result['error']]);
        }

        if (! $result['changed']) {
            return redirect()->route('admin.members.show', $member)->with('status', 'Role akun anggota ini memang sudah sesuai.');
        }

        $newRoleName = $validated['role'] === 'pengurus' ? 'Pengurus' : 'Anggota';
        $dashboardRoute = $validated['role'] === 'pengurus' ? 'pengurus.dashboard' : 'member.dashboard';
        DashboardNotifier::user(
            $result['user'],
            'Role akun diperbarui',
            'Admin mengubah role akun Anda menjadi '.$newRoleName.'. Keluar lalu masuk kembali agar menu dan hak akses diperbarui.',
            route($dashboardRoute),
        );

        return redirect()->route('admin.members.show', $member)->with(
            'status',
            'Role akun '.$member->full_name.' berhasil diubah menjadi '.$newRoleName.'. Jenis dan status keanggotaan tidak berubah.',
        );
    }

    private function rules(?Member $member = null): array
    {
        $nraUniqueRule = Rule::unique('members', 'nra');

        if ($member !== null) {
            $nraUniqueRule->ignore($member->id);
        }

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'alam_name' => ['nullable', 'string', 'max:255'],
            'nra' => ['required', 'string', 'max:50', $nraUniqueRule],
            'nim' => ['nullable', 'string', 'max:30'],
            'membership_type' => ['required', Rule::in([Member::TYPE_PERMANENT, Member::TYPE_SPECIAL])],
            'status' => ['required', Rule::in([Member::STATUS_ACTIVE, Member::STATUS_INACTIVE])],
            'position' => ['nullable', 'string', 'max:255'],
            'profile_photo' => [$member === null ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::PROFILE_PHOTO_MAX_KILOBYTES],
            'address' => [$member === null ? 'required' : 'nullable', 'string', 'max:2000'],
            'phone' => ['required', 'string', 'max:30'],
        ];
    }

    private function recordChanges(Member $member, int $adminId, array $oldValues, array $newValues): void
    {
        foreach (self::AUDITED_FIELDS as $field => $label) {
            $oldValue = $oldValues[$field] ?? null;
            $newValue = $newValues[$field] ?? null;

            if ($oldValues !== [] && $oldValue === $newValue) {
                continue;
            }

            MemberChangeLog::query()->create([
                'member_id' => $member->id,
                'changed_by' => $adminId,
                'field_name' => $field,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);
        }
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function ensureCanViewMembers(Request $request): void
    {
        abort_unless(in_array($request->user()?->role, ['admin', 'pengurus'], true), 403);
    }
}
