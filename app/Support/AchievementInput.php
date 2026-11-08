<?php

namespace App\Support;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AchievementInput
{
    /** @return array<string, mixed> */
    public function validateAndStore(Request $request, ?Achievement $achievement = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'level' => ['nullable', 'string', 'max:100'],
            'achievement_date' => ['nullable', 'date'],
            'organizer' => ['nullable', 'string', 'max:180'],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:30000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
        ]);

        $image = $request->file('image');
        $removeImage = $request->boolean('remove_image');
        unset($validated['image'], $validated['remove_image']);

        $validated['slug'] = $this->uniqueSlug($validated['title'], $achievement);
        $validated['image_path'] = $image?->store('achievements', 'public')
            ?? ($removeImage ? null : $achievement?->image_path);

        return $validated;
    }

    private function uniqueSlug(string $title, ?Achievement $achievement = null): string
    {
        $baseSlug = Str::slug($title) ?: 'prestasi';
        $slug = $baseSlug;
        $suffix = 2;
        $query = Achievement::query()->where('slug', $slug);

        if ($achievement !== null) {
            $query->where('id', '!=', $achievement->id);
        }

        while ($query->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
            $query = Achievement::query()->where('slug', $slug);

            if ($achievement !== null) {
                $query->where('id', '!=', $achievement->id);
            }
        }

        return $slug;
    }
}
