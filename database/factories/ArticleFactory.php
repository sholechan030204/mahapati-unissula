<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'slug' => fake()->unique()->slug(),
            'category' => fake()->randomElement(['Berita', 'Publikasi', 'Pengumuman']),
            'excerpt' => fake()->sentence(18),
            'body' => fake()->paragraphs(3, true),
            'cover_image' => null,
            'status' => Article::STATUS_DRAFT,
            'rejection_reason' => null,
            'created_by' => null,
            'reviewed_by' => null,
            'published_at' => null,
        ];
    }
}
