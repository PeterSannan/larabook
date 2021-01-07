<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LikesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_post()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(); 
        
        $response = $this->post('/api/posts/' . $post->id . '/likes')->assertStatus(201);
        $this->assertCount(1, $post->likes);
    }

    public function test_post_are_returned_with_likes()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create([
            'user_id' => $user->id
        ]); 
        
        $this->post('/api/posts/' . $post->id . '/likes')->assertStatus(201); 
        $this->assertCount(1, $post->likes);
        
        $this->get('/api/posts/')
            ->assertJson([
                'data' => [
                    [
                        'type' => 'posts',
                        'id' => $post->id,
                        'attributes' => [
                            'likes_count' => 1,
                            'auth_user_liked' => 1
                        ],
                        'links'=>[
                            'self' => url('/api/posts/'.$post->id)
                        ]
                    ]
                ]
            ]);
    }
}
