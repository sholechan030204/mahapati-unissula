<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\View\View;

class PublicAchievementController extends Controller
{
    public function index(): View
    {
        return view('public.achievements', [
            'achievements' => Achievement::query()
                ->where('status', Achievement::STATUS_PUBLISHED)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('achievement_date')
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    public function show(string $slug): View
    {
        $achievement = Achievement::query()
            ->where('slug', $slug)
            ->where('status', Achievement::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('public.achievements', ['achievement' => $achievement]);
    }
}
