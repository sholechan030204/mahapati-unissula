<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\GalleryChangeRequest;
use App\Models\GalleryItem;
use App\Models\Article;
use App\Models\Achievement;
use App\Support\GalleryItemInput;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GallerySubmissionController extends Controller
{
    public function __construct(private GalleryItemInput $galleryItemInput) {}

    public function index(Request $request): View
    {
        $submissions = GalleryChangeRequest::query()
            ->where('proposed_by', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('member.gallery-submissions.index', [
            'submissions' => $submissions,
            'articles' => Article::query()->where('created_by', $request->user()->id)->latest()->paginate(8, ['*'], 'article_page'),
            'achievements' => Achievement::query()->where('created_by', $request->user()->id)->latest()->paginate(8, ['*'], 'achievement_page'),
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery.form', [
            'galleryItem' => new GalleryItem(['media_type' => GalleryItem::TYPE_PHOTO]),
            'isEditing' => false,
            'isProposal' => true,
            'routePrefix' => 'member',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->galleryItemInput->validateAndStore($request, isProposal: true);
        GalleryChangeRequest::query()->create([
            'action' => GalleryChangeRequest::ACTION_CREATE,
            'payload' => $payload,
            'status' => GalleryChangeRequest::STATUS_PENDING,
            'proposed_by' => $request->user()->id,
        ]);
        DashboardNotifier::admins('Kiriman galeri baru', $request->user()->name.' mengirim foto untuk ditinjau.', route('admin.gallery-requests.index'));

        return redirect()->route('member.gallery-submissions.index')->with('status', 'Kiriman galeri terkirim dan menunggu persetujuan Admin.');
    }
}
