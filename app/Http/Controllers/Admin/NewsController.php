<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Support\ArticleInput;
use App\Support\DashboardNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(private ArticleInput $articleInput) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in([Article::STATUS_DRAFT, Article::STATUS_PENDING, Article::STATUS_PUBLISHED, Article::STATUS_REJECTED])],
        ]);
        $query = Article::query()
            ->with(['createdBy:id,name', 'reviewedBy:id,name'])
            ->when(($filters['q'] ?? '') !== '', function (Builder $query) use ($filters): void {
                $term = '%'.$filters['q'].'%';
                $query->where(function (Builder $articleQuery) use ($term): void {
                    $articleQuery->where('title', 'like', $term)
                        ->orWhere('excerpt', 'like', $term)
                        ->orWhere('category', 'like', $term);
                });
            })
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']));

        return view('admin.news.index', [
            'articles' => $query->latest()->paginate(12)->withQueryString(),
            'filters' => $filters,
            'publishedCount' => Article::query()->where('status', Article::STATUS_PUBLISHED)->count(),
            'draftCount' => Article::query()->where('status', Article::STATUS_DRAFT)->count(),
            'pendingCount' => Article::query()->where('status', Article::STATUS_PENDING)->count(),
            'rejectedCount' => Article::query()->where('status', Article::STATUS_REJECTED)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.news.form', [
            'article' => new Article(['category' => 'Berita', 'status' => Article::STATUS_DRAFT]),
            'isEditing' => false,
            'isSubmission' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $status = $request->validate(['status' => ['required', Rule::in([Article::STATUS_DRAFT, Article::STATUS_PUBLISHED])]])['status'];
        $validated = $this->articleInput->validateAndStore($request);
        $validated['status'] = $status;
        $validated['created_by'] = $request->user()->id;
        $validated['published_at'] = $status === Article::STATUS_PUBLISHED ? now() : null;
        $article = Article::query()->create($validated);

        return redirect()->route('admin.news.show', $article)->with('status', 'Berita berhasil disimpan.');
    }

    public function show(Article $article): View
    {
        return view('admin.news.show', ['article' => $article->load(['createdBy', 'reviewedBy'])]);
    }

    public function edit(Article $article): View
    {
        return view('admin.news.form', [
            'article' => $article,
            'isEditing' => true,
            'isSubmission' => false,
            'routePrefix' => 'admin',
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $status = $request->validate(['status' => ['required', Rule::in([Article::STATUS_DRAFT, Article::STATUS_PUBLISHED])]])['status'];
        $validated = $this->articleInput->validateAndStore($request, $article);
        $oldCover = $article->cover_image;
        $validated['status'] = $status;
        $validated['published_at'] = $status === Article::STATUS_PUBLISHED ? ($article->published_at ?? now()) : null;
        $validated['rejection_reason'] = null;
        $validated['reviewed_by'] = null;
        $article->update($validated);

        if ($oldCover !== null && $oldCover !== $article->cover_image) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('admin.news.show', $article)->with('status', 'Berita berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $cover = $article->cover_image;
        $article->delete();

        if ($cover !== null) {
            Storage::disk('public')->delete($cover);
        }

        return redirect()->route('admin.news.index')->with('status', 'Berita berhasil dihapus.');
    }

    public function approve(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->status === Article::STATUS_PENDING, 409, 'Kiriman ini sudah pernah diproses.');
        $article->update([
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now(),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
        $submissionRoute = $article->createdBy?->role === 'pengurus' ? 'pengurus.news.index' : 'member.news-submissions.index';
        DashboardNotifier::user($article->createdBy, 'Berita disetujui', '“'.$article->title.'” telah disetujui dan diterbitkan.', route($submissionRoute));

        return redirect()->route('admin.news.show', $article)->with('status', 'Berita disetujui dan diterbitkan ke website publik.');
    }

    public function reject(Request $request, Article $article): RedirectResponse
    {
        abort_unless($article->status === Article::STATUS_PENDING, 409, 'Kiriman ini sudah pernah diproses.');
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'min:3', 'max:2000']]);
        $article->update([
            'status' => Article::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'published_at' => null,
        ]);
        $submissionRoute = $article->createdBy?->role === 'pengurus' ? 'pengurus.news.index' : 'member.news-submissions.index';
        DashboardNotifier::user($article->createdBy, 'Berita perlu diperbaiki', '“'.$article->title.'” ditolak. Alasan Admin: '.$validated['rejection_reason'], route($submissionRoute));

        return redirect()->route('admin.news.show', $article)->with('status', 'Kiriman berita ditolak. Alasan sudah dicatat.');
    }
}
