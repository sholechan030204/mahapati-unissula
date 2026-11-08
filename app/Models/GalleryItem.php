<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'caption', 'media_type', 'photo_path', 'youtube_url', 'activity_date', 'created_by'])]
class GalleryItem extends Model
{
    /** @use HasFactory<\Database\Factories\GalleryItemFactory> */
    use HasFactory;

    public const TYPE_PHOTO = 'photo';

    public const TYPE_YOUTUBE = 'youtube';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function youtubeThumbnailUrl(): ?string
    {
        if ($this->media_type !== self::TYPE_YOUTUBE || $this->youtube_url === null) {
            return null;
        }

        $host = strtolower((string) parse_url($this->youtube_url, PHP_URL_HOST));
        $videoId = null;

        if (in_array($host, ['youtu.be', 'www.youtu.be'], true)) {
            $videoId = trim((string) parse_url($this->youtube_url, PHP_URL_PATH), '/');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            parse_str((string) parse_url($this->youtube_url, PHP_URL_QUERY), $query);
            $videoId = $query['v'] ?? null;

            if (! is_string($videoId)) {
                $path = trim((string) parse_url($this->youtube_url, PHP_URL_PATH), '/');
                $segments = explode('/', $path);
                $videoId = in_array($segments[0] ?? null, ['embed', 'shorts', 'live'], true) ? ($segments[1] ?? null) : null;
            }
        }

        return is_string($videoId) && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId) === 1
            ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg"
            : null;
    }

    protected function casts(): array
    {
        return ['activity_date' => 'date'];
    }
}
