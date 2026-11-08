<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberChangeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberDomicileController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
            'domicile' => ['nullable', 'in:available,missing'],
        ]);
        $members = Member::query()
            ->when(($filters['q'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $term = '%'.trim($filters['q']).'%';
                $query->where(function (Builder $search) use ($term): void {
                    $search->where('full_name', 'like', $term)->orWhere('nra', 'like', $term)
                        ->orWhere('nim', 'like', $term)->orWhere('address', 'like', $term);
                });
            })
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->when(($filters['domicile'] ?? '') === 'available', fn (Builder $query): Builder => $query->whereNotNull('address')->where('address', '!=', ''))
            ->when(($filters['domicile'] ?? '') === 'missing', fn (Builder $query): Builder => $query->where(fn (Builder $empty): Builder => $empty->whereNull('address')->orWhere('address', '')))
            ->orderBy('full_name')->paginate(15)->withQueryString();

        return view('admin.domiciles.index', [
            'members' => $members,
            'filters' => $filters,
            'totalMembers' => Member::query()->count(),
            'withDomicile' => Member::query()->whereNotNull('address')->where('address', '!=', '')->count(),
            'withoutDomicile' => Member::query()->where(fn (Builder $query): Builder => $query->whereNull('address')->orWhere('address', ''))->count(),
        ]);
    }

    public function editMine(Request $request): View
    {
        $member = $request->user()->member;

        return view('member.domicile.form', ['member' => $member]);
    }

    public function updateMine(Request $request): RedirectResponse
    {
        $member = $request->user()->member;
        abort_unless($member instanceof Member, 403, 'Akun ini belum terhubung ke data anggota. Hubungi Admin.');
        $validated = $request->validate(['address' => ['required', 'string', 'max:2000']]);
        $oldAddress = $member->address;
        $member->update(['address' => trim($validated['address'])]);

        if ($oldAddress !== $member->address) {
            MemberChangeLog::query()->create([
                'member_id' => $member->id,
                'changed_by' => $request->user()->id,
                'field_name' => 'address',
                'old_value' => $oldAddress,
                'new_value' => $member->address,
            ]);
        }

        return redirect()->route('member.domicile.edit')->with('status', 'Domisili berhasil diperbarui. Perubahan tercatat pada riwayat data anggota.');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        $oldAddress = $member->address;

        if ($oldAddress !== null && $oldAddress !== '') {
            $member->update(['address' => null]);
            MemberChangeLog::query()->create([
                'member_id' => $member->id,
                'changed_by' => $request->user()->id,
                'field_name' => 'address',
                'old_value' => $oldAddress,
                'new_value' => null,
            ]);
        }

        return redirect()->route('admin.domiciles.index')->with('status', $oldAddress ? 'Data domisili berhasil dihapus. Riwayat penghapusan tercatat.' : 'Anggota ini belum memiliki data domisili.');
    }
}
