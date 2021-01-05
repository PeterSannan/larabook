<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RetreivePostsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_retreive_posts()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class, 2)->create([
            'user_id' => $user->id
        ]);

        $response = $this->get('/api/posts');

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'type' => 'posts',
                    'id' => $posts->last()->id,
                    'attributes' => [
                        'body' =>  $posts->last()->body,
                    ]

                ],
                [
                    'type' => 'posts',
                    'id' => $posts->first()->id,
                    'attributes' => [
                        'body' =>  $posts->first()->body,
                    ]
                ]
            ],
        ]);
    }

    public function test_user_can_only_retreive_his_posts()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class, 2)->create();

        $response = $this->get('/api/posts')
            ->assertExactJson([
                'data' => []
            ]);
    }
}
