<?php

namespace Database\Factories;

use App\Helpers\FileHelper;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $image = UploadedFile::fake()->image('post-image.jpg');
        $fileName = FileHelper::generateName($image->getClientOriginalExtension());
        Storage::putFileAs((new Post)->storage_name, $image, $fileName);
        return [
            'title' => $title,
            'content' => fake()->realText(config('attribute.content.max')),
            'slug' => Str::slug($title),
            'file' => $fileName,
            'created_by' => User::factory(),
        ];
    }
}
