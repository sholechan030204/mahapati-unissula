<?php

namespace Database\Factories;

use App\Models\GalleryChangeRequest;
use App\Models\GalleryItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryChangeRequest>
 */
class GalleryChangeRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gallery_item_id' => null,
            'action' => GalleryChangeRequest::ACTION_CREATE,
            'payload' => [
                'title' => fake()->sentence(4),
                'caption' => fake()->paragraph(),
                'media_type' => GalleryItem::TYPE_PHOTO,
                'photo_path' => 'gallery-items/example.jpg',
                'youtube_url' => null,
                'activity_date' => fake()->date(),
            ],
            'status' => GalleryChangeRequest::STATUS_PENDING,
            'rejection_reason' => null,
            'proposed_by' => User::factory(),
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }
}
