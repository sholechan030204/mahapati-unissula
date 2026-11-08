<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryChangeRequest;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryManagementController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->user()->role;
        $items = GalleryItem::query()
            ->with('createdBy')
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = trim((string) $request->string('q'));
                $query->where(function (Builder $galleryQuery) use ($term): void {
                    $galleryQuery->where('title', 'like', "%{$term}%")
                        ->orWhere('caption', 'like', "%{$term}%");
                });
            })
            ->when(in_array($request->query('type'), [GalleryItem::TYPE_PHOTO, GalleryItem::TYPE_YOUTUBE], true), function (Builder $query) use ($request): void {
                $query->where('media_type', $request->query('type'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.gallery.index', [
            'items' => $items,
            'isAdmin' => $role === 'admin',
            'isPengurus' => $role === 'pengurus',
            'photoCount' => GalleryItem::query()->where('media_type', GalleryItem::TYPE_PHOTO)->count(),
            'videoCount' => GalleryItem::query()->where('media_type', GalleryItem::TYPE_YOUTUBE)->count(),
            'pendingRequestCount' => $role === 'admin'
                ? GalleryChangeRequest::query()->where('status', GalleryChangeRequest::STATUS_PENDING)->count()
                : GalleryChangeRequest::query()->where('status', GalleryChangeRequest::STATUS_PENDING)->where('proposed_by', $request->user()->id)->count(),
            'myRequests' => $role === 'pengurus'
                ? GalleryChangeRequest::query()->where('proposed_by', $request->user()->id)->latest()->limit(6)->get()
                : collect(),
        ]);
    }

    public function show(Request $request, GalleryItem $galleryItem): View
    {
        $role = $request->user()->role;

        return view('admin.gallery.show', [
            'galleryItem' => $galleryItem->load('createdBy'),
            'isProposal' => $role === 'pengurus',
            'isReadOnly' => $role === 'anggota',
            'routePrefix' => $role === 'admin' ? 'admin' : 'pengurus',
        ]);
    }
}
