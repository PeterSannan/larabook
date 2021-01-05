<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostToTimelineTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_post_a_text_post()
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
}
