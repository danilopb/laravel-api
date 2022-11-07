<?php

namespace Tests\Unit\Requests;

use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StorePostRequestTest extends TestCase
{
    public function test_should_fail_when_title_is_not_provided()
    {
        $data = [
            'content' => fake()->realText(config('attribute.content.max')),
            'file' => UploadedFile::fake()->image('post-image.jpg')
        ];
        $response = $this->post(route('posts.store'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(422);
        $response->assertJson(function (AssertableJson $json) use ($data) {
            $json->has('message')
                ->has('errors', 1)
                ->whereAllType([
                    'errors.title' => 'array',
                ]);
        });
    }

    public function test_should_fail_when_content_is_not_provided()
    {
        $data = [
            'title' => fake()->realText(config('attribute.title.max')),
            'file' => UploadedFile::fake()->image('post-image.jpg')
        ];
        $response = $this->post(route('posts.store'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(422);
        $response->assertJson(function (AssertableJson $json) use ($data) {
            $json->has('message')
                ->has('errors', 1)
                ->whereAllType([
                    'errors.content' => 'array',
                ]);
        });
    }

    public function test_should_fail_when_file_is_not_provided()
    {
        $data = [
            'title' => fake()->realText(config('attribute.title.max')),
            'content' => fake()->realText(config('attribute.content.max')),
        ];
        $response = $this->post(route('posts.store'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(422);
        $response->assertJson(function (AssertableJson $json) use ($data) {
            $json->has('message')
                ->has('errors', 1)
                ->whereAllType([
                    'errors.file' => 'array',
                ]);
        });
    }
}
