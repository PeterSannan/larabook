<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_post_a_text_post()  //check image
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/posts', [
            'data' => [
                'type' => "posts",
                'attributes' => [
                    'body' => 'testing body'
                ]
            ]
        ]);

        $post = Post::first();

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => [
                    'body' => $post->body,
                    'posted_at' => $post->created_at->diffForHumans(),
                    'image' => $post->image,
                    'posted_by' => [
                        'attributes' => [
                            'name' => $user->name
                        ]
                    ],
                ],
                'links' => [
                    'self' => url('/api/posts/' . $post->id)
                ]
            ]
        ]);
    }

    public function test_user_can_post_a_image_post()  //check image
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = factory(User::class)->create(), 'api');

        $image = UploadedFile::fake()->image('postimg.png');

        $response = $this->post('/api/posts', [
            'body' => 'testing body',
            'image' => $image
        ]);

        Storage::disk('s3')->assertExists('posts-images/' . $image->hashName());

        $post = Post::first();

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => [
                    'body' => $post->body,
                    'image' => Config::get('aws.s3_url') . ($post->image)
                ],
                'links' => [
                    'self' => url('/api/posts/' . $post->id)
                ]
            ]
        ]);
    }
}
