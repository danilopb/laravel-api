<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->realText(config('attribute.title.max'));
        return [
            'title' => $title,
            'content' => fake()->realText(config('attribute.content.max')),
            'slug' => Str::slug($title),
            'created_by' => User::factory(),
//            'status'
//            'category'
        ];
    }
}
