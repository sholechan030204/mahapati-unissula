<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryChangeRequest;
use App\Models\GalleryItem;
use App\Support\GalleryItemInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private GalleryItemInput $galleryItemInput) {}

    public function create(): View
    {
        return view('admin.gallery.form', [
            'galleryItem' => new GalleryItem(['media_type' => GalleryItem::TYPE_PHOTO]),
            'isEditing' => false,
            'isProposal' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->galleryItemInput->validateAndStore($request);
        $galleryItem = GalleryItem::query()->create($data + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.gallery.show', $galleryItem)->with('status', 'Materi galeri berhasil diterbitkan.');
    }

    public function show(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.show', [
            'galleryItem' => $galleryItem->load('createdBy'),
            'isProposal' => false,
            'isReadOnly' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.form', [
            'galleryItem' => $galleryItem,
            'isEditing' => true,
            'isProposal' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $data = $this->galleryItemInput->validateAndStore($request, $galleryItem);
        $oldPhotoPath = $galleryItem->photo_path;

        DB::transaction(function () use ($galleryItem, $data, $request): void {
            $this->rejectPendingRequests($galleryItem, $request->user()->id, 'Usulan digantikan oleh perubahan langsung dari Admin.');
            $galleryItem->update($data);
        });

        if ($oldPhotoPath !== null && $oldPhotoPath !== $galleryItem->photo_path) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()->route('admin.gallery.show', $galleryItem)->with('status', 'Materi galeri berhasil diperbarui.');
    }

    public function destroy(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $photoPath = $galleryItem->photo_path;

        DB::transaction(function () use ($galleryItem, $request): void {
            $this->rejectPendingRequests($galleryItem, $request->user()->id, 'Materi dihapus langsung oleh Admin sebelum usulan diproses.');
            $galleryItem->delete();
        });

        if ($photoPath !== null) {
            Storage::disk('public')->delete($photoPath);
        }

        return redirect()->route('admin.gallery.index')->with('status', 'Materi galeri berhasil dihapus.');
    }

    private function rejectPendingRequests(GalleryItem $galleryItem, int $adminId, string $reason): void
    {
        $requests = GalleryChangeRequest::query()
            ->where('gallery_item_id', $galleryItem->id)
            ->where('status', GalleryChangeRequest::STATUS_PENDING)
            ->get();

        foreach ($requests as $galleryChangeRequest) {
            $proposedPhoto = $galleryChangeRequest->payload['photo_path'] ?? null;

            if ($proposedPhoto !== null && $proposedPhoto !== $galleryItem->photo_path) {
                if (str_starts_with($proposedPhoto, 'gallery-proposals/')) {
                    Storage::disk('local')->delete($proposedPhoto);
                } else {
                    Storage::disk('public')->delete($proposedPhoto);
                }
            }

            $galleryChangeRequest->update([
                'status' => GalleryChangeRequest::STATUS_REJECTED,
                'rejection_reason' => $reason,
                'reviewed_by' => $adminId,
                'reviewed_at' => now(),
            ]);
        }
    }
}
