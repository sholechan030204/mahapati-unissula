<?php

namespace Database\Factories;

use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'caption' => fake()->paragraph(),
            'media_type' => GalleryItem::TYPE_PHOTO,
            'photo_path' => 'gallery-items/example.jpg',
            'youtube_url' => null,
            'activity_date' => fake()->optional()->date(),
            'created_by' => null,
        ];
    }
}
