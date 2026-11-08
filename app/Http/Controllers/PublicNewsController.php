<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class PublicNewsController extends Controller
{
    public function index(): View
    {
        return view('public.news', [
            'articles' => Article::query()
                ->where('status', Article::STATUS_PUBLISHED)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $article = Article::query()
            ->where('slug', $slug)
            ->where('status', Article::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('public.news', ['article' => $article]);
    }
}
