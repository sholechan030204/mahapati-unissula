<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\ArticleInput;
use App\Support\DashboardNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleSubmissionController extends Controller
{
    public function __construct(private ArticleInput $articleInput) {}

    public function index(Request $request): View
    {
        $routePrefix = $request->user()->role === 'pengurus' ? 'pengurus' : 'member';

        return view('admin.news.submissions', [
            'articles' => Article::query()->where('created_by', $request->user()->id)->latest()->paginate(10),
            'routePrefix' => $routePrefix,
        ]);
    }

    public function create(Request $request): View
    {
        $routePrefix = $request->user()->role === 'pengurus' ? 'pengurus' : 'member';

        return view('admin.news.form', [
            'article' => new Article(['category' => 'Berita', 'status' => Article::STATUS_PENDING]),
            'isEditing' => false,
            'isSubmission' => true,
            'routePrefix' => $routePrefix,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->articleInput->validateAndStore($request);
        $validated['status'] = Article::STATUS_PENDING;
        $validated['created_by'] = $request->user()->id;
        $validated['published_at'] = null;
        Article::query()->create($validated);
        DashboardNotifier::admins('Berita menunggu persetujuan', $validated['title'].' dikirim untuk ditinjau.', route('admin.news.index', ['status' => 'pending']));
        $routePrefix = $request->user()->role === 'pengurus' ? 'pengurus' : 'member';

        $destination = $routePrefix === 'pengurus' ? 'pengurus.news.index' : 'member.news-submissions.index';

        return redirect()->route($destination)->with('status', 'Berita terkirim dan menunggu persetujuan Admin.');
    }
}
