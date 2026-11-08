<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleInput
{
    /** @return array<string, mixed> */
    public function validateAndStore(Request $request, ?Article $article = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'max:30000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['sometimes', 'boolean'],
        ]);

        $cover = $request->file('cover_image');
        $removeCover = $request->boolean('remove_cover');
        unset($validated['cover_image'], $validated['remove_cover']);

        $validated['slug'] = $this->uniqueSlug($validated['title'], $article);
        $validated['cover_image'] = $cover?->store('articles', 'public')
            ?? ($removeCover ? null : $article?->cover_image);

        return $validated;
    }

    private function uniqueSlug(string $title, ?Article $article = null): string
    {
        $baseSlug = Str::slug($title) ?: 'artikel';
        $slug = $baseSlug;
        $suffix = 2;

        $query = Article::query()->where('slug', $slug);

        if ($article !== null) {
            $query->where('id', '!=', $article->id);
        }

        while ($query->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
            $query = Article::query()->where('slug', $slug);

            if ($article !== null) {
                $query->where('id', '!=', $article->id);
            }
        }

        return $slug;
    }
}
