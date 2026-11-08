<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\GalleryChangeRequest;
use App\Models\GalleryItem;
use App\Support\GalleryItemInput;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryProposalController extends Controller
{
    public function __construct(private GalleryItemInput $galleryItemInput) {}

    public function create(): View
    {
        return view('admin.gallery.form', [
            'galleryItem' => new GalleryItem(['media_type' => GalleryItem::TYPE_PHOTO]),
            'isEditing' => false,
            'isProposal' => true,
            'routePrefix' => 'pengurus',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->galleryItemInput->validateAndStore($request, isProposal: true);
        $this->createRequest($request, GalleryChangeRequest::ACTION_CREATE, null, $payload);

        return redirect()->route('pengurus.gallery.index')->with('status', 'Usulan materi galeri dikirim kepada Admin untuk ditinjau.');
    }

    public function edit(GalleryItem $galleryItem): View
    {
        $this->ensureNoPendingRequest($galleryItem);

        return view('admin.gallery.form', [
            'galleryItem' => $galleryItem,
            'isEditing' => true,
            'isProposal' => true,
            'routePrefix' => 'pengurus',
        ]);
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $this->ensureNoPendingRequest($galleryItem);
        $payload = $this->galleryItemInput->validateAndStore($request, $galleryItem, isProposal: true);
        $this->createRequest($request, GalleryChangeRequest::ACTION_UPDATE, $galleryItem->id, $payload);

        return redirect()->route('pengurus.gallery.index')->with('status', 'Usulan perubahan galeri dikirim kepada Admin.');
    }

    public function destroy(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $this->ensureNoPendingRequest($galleryItem);
        $this->createRequest($request, GalleryChangeRequest::ACTION_DELETE, $galleryItem->id, null);

        return redirect()->route('pengurus.gallery.index')->with('status', 'Usulan penghapusan galeri dikirim kepada Admin.');
    }

    private function createRequest(Request $request, string $action, ?int $galleryItemId, ?array $payload): void
    {
        GalleryChangeRequest::query()->create([
            'gallery_item_id' => $galleryItemId,
            'action' => $action,
            'payload' => $payload,
            'status' => GalleryChangeRequest::STATUS_PENDING,
            'proposed_by' => $request->user()->id,
        ]);
        DashboardNotifier::admins('Usulan galeri baru', $request->user()->name.' mengirim usulan materi galeri untuk ditinjau.', route('admin.gallery-requests.index'));
    }

    private function ensureNoPendingRequest(GalleryItem $galleryItem): void
    {
        abort_if(
            GalleryChangeRequest::query()
                ->where('gallery_item_id', $galleryItem->id)
                ->where('status', GalleryChangeRequest::STATUS_PENDING)
                ->exists(),
            409,
            'Masih ada usulan terkait materi ini yang menunggu persetujuan Admin.',
        );
    }
}
