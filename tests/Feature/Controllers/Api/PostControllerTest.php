<?php

namespace Controllers\Api;

use App\Http\Resources\V1\PostResource;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        config(['filesystems.default' => $this->fakeFileDriverName]);
        Storage::fake($this->fakeFileDriverName);
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
            'hasError' => false,
            'message' => '',
            'posts' => PostResource::collection($posts)->jsonSerialize()
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
            'file' => UploadedFile::fake()->image('post-image.jpg')
        ];
        $response = $this->post(route('posts.store'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $this->assertDatabaseHas((new Post())->getTable(),[
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
        $post = Post::where('title', $data['title'])
            ->where('content', $data['content'])
            ->first();
        Storage::disk($this->fakeFileDriverName)->assertExists($post->storage_name."/".$post->file);
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
            'file' => UploadedFile::fake()->image('post-image.jpg')
        ];
        $response = $this->put(route('posts.update', $post), $dataUpdate, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $this->assertDatabaseHas((new Post())->getTable(),[
            'id' => $post->id,
            'title' => $dataUpdate['title'],
            'content' => $dataUpdate['content'],
            'slug' => $dataUpdate['slug']
        ]);
        $oldFileName = $post->file;
        $newFileName = Post::find($post->id)->file;
        Storage::disk($this->fakeFileDriverName)->assertExists($post->storage_name."/".$newFileName);
        Storage::disk($this->fakeFileDriverName)->assertMissing($post->storage_name."/".$oldFileName);
    }

    /**
     * Test update post without slug the file don't change.
     *
     * @return void
     */
    public function test_update_without_slug(): void
    {
        $post = Post::factory()
            ->create();
        $title = fake()->realText(config('attribute.title.max'));
        $dataUpdate = [
            'title' => $title,
            'content' => fake()->realText(config('attribute.content.max')),
            'file' => UploadedFile::fake()->image('post-image.jpg'),
        ];
        $response = $this->put(route('posts.update', $post), $dataUpdate, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        $this->assertDatabaseHas((new Post())->getTable(),[
            'id' => $post->id,
            'title' => $dataUpdate['title'],
            'content' => $dataUpdate['content'],
            'slug' => $post->slug
        ]);
        $oldFileName = $post->file;
        $newFileName = Post::find($post->id)->file;
        Storage::disk($this->fakeFileDriverName)->assertExists($post->storage_name."/".$newFileName);
        Storage::disk($this->fakeFileDriverName)->assertMissing($post->storage_name."/".$oldFileName);
    }

    /**
     * Test update post without file, the file don't change.
     *
     * @return void
     */
    public function test_update_without_file(): void
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
            'id' => $post->id,
            'title' => $dataUpdate['title'],
            'content' => $dataUpdate['content'],
            'slug' => $dataUpdate['slug'],
            'file' => $post->file,
        ]);
        Storage::disk($this->fakeFileDriverName)->assertExists($post->storage_name."/".$post->file);
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
            $data['file'][] = UploadedFile::fake()->image('post-image.jpg');
        }
        $response = $this->post(route('posts.bulk'), $data, $this->getApiHeader($this->token));
        $response->assertStatus(200);
        for ($i = 0 ; $i < $quantity; $i++) {
            $this->assertDatabaseHas((new Post())->getTable(), [
                'title' => $data['title'][$i],
                'content' => $data['content'][$i],
            ]);
            $post = Post::where('title', $data['title'][$i])
                ->where('content', $data['content'][$i])
                ->first();
            Storage::disk($this->fakeFileDriverName)->assertExists($post->storage_name."/".$post->file);
        }
    }
}
