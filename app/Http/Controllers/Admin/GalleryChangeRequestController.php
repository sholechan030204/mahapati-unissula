<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryChangeRequest;
use App\Models\GalleryItem;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GalleryChangeRequestController extends Controller
{
    public function index(): View
    {
        $requests = GalleryChangeRequest::query()
            ->with(['proposedBy', 'reviewedBy', 'galleryItem'])
            ->latest()
            ->paginate(10);

        return view('admin.gallery-requests.index', ['requests' => $requests]);
    }

    public function photo(GalleryChangeRequest $galleryChangeRequest): BinaryFileResponse
    {
        abort_unless(in_array($galleryChangeRequest->status, [GalleryChangeRequest::STATUS_PENDING, GalleryChangeRequest::STATUS_APPROVED], true), 404);

        $path = $galleryChangeRequest->payload['photo_path'] ?? null;
        abort_unless(is_string($path) && $path !== '', 404);

        $disk = str_starts_with($path, 'gallery-proposals/') && $galleryChangeRequest->status === GalleryChangeRequest::STATUS_PENDING
            ? 'local'
            : 'public';
        if ($disk === 'public' && str_starts_with($path, 'gallery-proposals/')) {
            $path = 'gallery-items/'.basename($path);
        }

        abort_unless(Storage::disk($disk)->exists($path), 404);

        return response()->file(Storage::disk($disk)->path($path));
    }

    public function approve(Request $request, GalleryChangeRequest $galleryChangeRequest): RedirectResponse
    {
        abort_unless($galleryChangeRequest->status === GalleryChangeRequest::STATUS_PENDING, 409, 'Kiriman ini sudah ditinjau.');

        $oldPhotoPath = null;
        $payload = $galleryChangeRequest->payload ?? [];
        $stagedPhotoPath = $payload['photo_path'] ?? null;
        $publishedPhotoPath = null;

        if (is_string($stagedPhotoPath) && str_starts_with($stagedPhotoPath, 'gallery-proposals/')) {
            abort_unless(Storage::disk('local')->exists($stagedPhotoPath), 404, 'Foto usulan tidak ditemukan.');
            $publishedPhotoPath = 'gallery-items/'.basename($stagedPhotoPath);
            Storage::disk('public')->put($publishedPhotoPath, Storage::disk('local')->get($stagedPhotoPath));
            $payload['photo_path'] = $publishedPhotoPath;
        }

        try {
            DB::transaction(function () use ($request, $galleryChangeRequest, &$oldPhotoPath, $payload): void {
                if ($galleryChangeRequest->action === GalleryChangeRequest::ACTION_CREATE) {
                    $galleryItem = GalleryItem::query()->create($payload + ['created_by' => $galleryChangeRequest->proposed_by]);
                    $galleryChangeRequest->gallery_item_id = $galleryItem->id;
                } elseif ($galleryChangeRequest->action === GalleryChangeRequest::ACTION_UPDATE) {
                    $galleryItem = GalleryItem::query()->findOrFail($galleryChangeRequest->gallery_item_id);
                    $oldPhotoPath = $galleryItem->photo_path;
                    $galleryItem->update($payload);
                } elseif ($galleryChangeRequest->action === GalleryChangeRequest::ACTION_DELETE) {
                    $galleryItem = GalleryItem::query()->findOrFail($galleryChangeRequest->gallery_item_id);
                    $oldPhotoPath = $galleryItem->photo_path;
                    $galleryItem->delete();
                } else {
                    abort(422, 'Jenis usulan galeri tidak dikenali.');
                }

                $galleryChangeRequest->update([
                    'status' => GalleryChangeRequest::STATUS_APPROVED,
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            if ($publishedPhotoPath !== null) {
                Storage::disk('public')->delete($publishedPhotoPath);
            }

            throw $exception;
        }

        if ($stagedPhotoPath !== null && str_starts_with($stagedPhotoPath, 'gallery-proposals/')) {
            Storage::disk('local')->delete($stagedPhotoPath);
        }

        $newPhotoPath = $galleryChangeRequest->action === GalleryChangeRequest::ACTION_CREATE
            ? null
            : ($galleryChangeRequest->payload['photo_path'] ?? null);

        if ($oldPhotoPath !== null && $oldPhotoPath !== $newPhotoPath) {
            Storage::disk('public')->delete($oldPhotoPath);
        }
        $submissionRoute = $galleryChangeRequest->proposedBy?->role === 'pengurus' ? 'pengurus.gallery.index' : 'member.gallery-submissions.index';
        DashboardNotifier::user($galleryChangeRequest->proposedBy, 'Usulan galeri disetujui', 'Usulan materi galeri Anda telah disetujui oleh Admin.', route($submissionRoute));

        return redirect()->route('admin.gallery-requests.index')->with('status', 'Kiriman disetujui dan perubahan diterapkan pada galeri.');
    }

    public function reject(Request $request, GalleryChangeRequest $galleryChangeRequest): RedirectResponse
    {
        abort_unless($galleryChangeRequest->status === GalleryChangeRequest::STATUS_PENDING, 409, 'Kiriman ini sudah ditinjau.');
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $this->removeUnapprovedPhoto($galleryChangeRequest);
        $galleryChangeRequest->update([
            'status' => GalleryChangeRequest::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        $submissionRoute = $galleryChangeRequest->proposedBy?->role === 'pengurus' ? 'pengurus.gallery.index' : 'member.gallery-submissions.index';
        DashboardNotifier::user($galleryChangeRequest->proposedBy, 'Usulan galeri ditolak', 'Usulan materi galeri Anda ditolak. Alasan Admin: '.$validated['rejection_reason'], route($submissionRoute));

        return redirect()->route('admin.gallery-requests.index')->with('status', 'Kiriman ditolak. Alasan penolakan tercatat.');
    }

    private function removeUnapprovedPhoto(GalleryChangeRequest $galleryChangeRequest): void
    {
        $proposedPhotoPath = $galleryChangeRequest->payload['photo_path'] ?? null;

        if ($proposedPhotoPath === null) {
            return;
        }

        $currentPhotoPath = $galleryChangeRequest->gallery_item_id !== null
            ? GalleryItem::query()->whereKey($galleryChangeRequest->gallery_item_id)->value('photo_path')
            : null;

        if (str_starts_with($proposedPhotoPath, 'gallery-proposals/')) {
            Storage::disk('local')->delete($proposedPhotoPath);
        } elseif ($proposedPhotoPath !== $currentPhotoPath) {
            Storage::disk('public')->delete($proposedPhotoPath);
        }
    }
}
