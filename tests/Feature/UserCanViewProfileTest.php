<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserCanViewProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_view_user_profile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        factory(Post::class)->create();

        $response = $this->get('api/users/'.$user->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'name' => $user->name
                    ],
                    'links' => [
                        'self' => url('api/users/'.$user->id)
                    ]
                ]
            ]);
    }

     /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_view_posts_of_a_profile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->get("/api/users/$user->id/posts");
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'type' => 'posts',
                        'id' => $post->id,
                        'attributes' =>  [
                            'body' => $post->body,
                            'posted_at' => $post->created_at->diffForHumans(),
                            'image' => $post->image,
                            'posted_by' => [
                                'type' => 'users',
                                'id' => $user->id,
                                'attributes' => [
                                    'name' => $user->name
                                ]
                            ]
                        ],
                        'links'=>[
                            'self' => url('/api/posts/'.$post->id)
                        ]
                    ]
                ]
            ]);
    }
}
