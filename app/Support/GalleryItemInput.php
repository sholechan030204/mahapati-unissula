<?php

namespace App\Support;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GalleryItemInput
{
    /** @return array<string, mixed> */
    public function validateAndStore(Request $request, ?GalleryItem $galleryItem = null, bool $isProposal = false): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'caption' => ['nullable', 'string', 'max:3000'],
            'media_type' => ['required', Rule::in([GalleryItem::TYPE_PHOTO, GalleryItem::TYPE_YOUTUBE])],
            'activity_date' => ['nullable', 'date'],
            'photo' => [
                Rule::requiredIf(fn (): bool => $request->input('media_type') === GalleryItem::TYPE_PHOTO && $galleryItem?->photo_path === null),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'youtube_url' => [
                'required_if:media_type,youtube',
                'nullable',
                'url',
                'max:500',
                'regex:/^https?:\/\/(?:www\.|m\.)?(?:youtube\.com|youtu\.be)\//i',
            ],
        ]);

        $photo = $request->file('photo');
        unset($validated['photo']);

        $validated['photo_path'] = $validated['media_type'] === GalleryItem::TYPE_PHOTO
            ? ($photo?->store($isProposal ? 'gallery-proposals' : 'gallery-items', $isProposal ? 'local' : 'public') ?? $galleryItem?->photo_path)
            : null;
        $validated['youtube_url'] = $validated['media_type'] === GalleryItem::TYPE_YOUTUBE
            ? $validated['youtube_url']
            : null;

        return $validated;
    }
}
