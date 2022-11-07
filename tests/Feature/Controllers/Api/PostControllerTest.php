<?php

namespace Controllers\Api;

use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUserByToken();
        $this->token = $this->user->currentAccessToken();
    }

    /**
     * Test get posts.
     *
     * @return void
     */
    public function test_index(): void
    {
        $posts = Post::factory()
            ->count(5)
            ->create();
        $response = $this->get(route('posts.index'), $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $response->assertExactJson([
            "hasError" => false,
            "message" => '',
            "posts" => PostResource::collection($posts)->jsonSerialize()
        ]);
    }

    /**
     * Test store post.
     *
     * @return void
     */
    public function test_store(): void
    {
        $data = [
            'title' => fake()->realText(config('attribute.title.max')),
            'content' => fake()->realText(config('attribute.content.max')),
        ];
        $response = $this->post(route('posts.store'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $this->assertDatabaseHas((new Post())->getTable(),[
            'title' => $data['title'],
            'content' => $data['content']
        ]);
    }

    /**
     * Test update post.
     *
     * @return void
     */
    public function test_update(): void
    {
        $post = Post::factory()
            ->create();
        $title = fake()->realText(config('attribute.title.max'));
        $dataUpdate = [
            'title' => $title,
            'content' => fake()->realText(config('attribute.content.max')),
            'slug' => Str::slug($title),
        ];
        $response = $this->put(route('posts.update', $post), $dataUpdate, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $this->assertDatabaseHas((new Post())->getTable(),[
            'title' => $dataUpdate['title'],
            'content' => $dataUpdate['content'],
            'slug' => $dataUpdate['slug']
        ]);
    }

    /**
     * Test show post.
     *
     * @return void
     */
    public function test_show(): void
    {
        $post = Post::factory()
            ->create();
        $response = $this->get(route('posts.show', $post), $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $response->assertExactJson([
            "hasError" => false,
            "message" => '',
            "post" => (new PostResource($post))->jsonSerialize()
        ]);
    }

    /**
     * Test destroy post.
     *
     * @return void
     */
    public function test_destroy(): void
    {
        $post = Post::factory()
            ->create();
        // Check exists created posts with deleted is null
        $this->assertDatabaseHas((new Post())->getTable(),[
            'id' => $post->id,
            'deleted_at' => null
        ]);
        $response = $this->delete(route('posts.destroy', $post), [], $this->getApiHeader($this->token));
        $response->assertStatus(200);
        //Check logic deleted
        $this->assertDatabaseMissing((new Post())->getTable(),[
            'id' => $post->id,
            'deleted_at' => null
        ]);
    }

    /**
     * Test force destroy post.
     *
     * @return void
     */
    public function test_force_destroy(): void
    {
        $post = Post::factory()
            ->create();
        // Check exists created posts
        $this->assertDatabaseHas((new Post())->getTable(),[
            'id' => $post->id,
        ]);
        $response = $this->delete(route('posts.force_destroy', $post), [], $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $this->assertDatabaseMissing((new Post())->getTable(),[
            'id' => $post->id,
        ]);
    }

    /**
     * Test bulk posts.
     *
     * @return void
     */
    public function test_bulk_store(): void
    {
        $quantity = 5;
        $data = [];
        for ($i = 0 ; $i < $quantity; $i++) {
            $data['title'][] = fake()->realText(config('attribute.title.max'));
            $data['content'][] = fake()->realText(config('attribute.content.max'));
        }
        $response = $this->post(route('posts.bulk'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        for ($i = 0 ; $i < $quantity; $i++) {
            $this->assertDatabaseHas((new Post())->getTable(), [
                'title' => $data['title'][$i],
                'content' => $data['content'][$i],
            ]);
        }
    }
}
