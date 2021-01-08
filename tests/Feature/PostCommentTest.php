<?php

namespace Tests\Feature;

use App\Comment;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCommentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_post_comment()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create();

        $response = $this->post('api/posts/' . $post->id . '/comments', [
            'comment' => 'test comment'
        ]);

        $comment = Comment::first();

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'comments',
                    'id' => $comment->id,
                    'attributes' => [
                        'commented_by' => [
                            'type' => 'users',
                            'id' => $comment->user_id,
                            'attributes' => [
                                'name' => $user->name
                            ]
                        ],
                        'post_id' => $post->id,
                        'comment' => $comment->comment,
                        'comment_at' => $comment->created_at->diffForHumans()
                    ],
                    'links' => [
                        'self' => url('/api/posts/' . $post->id)
                    ]
                ]
            ]);

        $this->assertCount(1, $post->comments);
    }

    public function test_body_is_required_to_post_comment()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create();

        $response = $this->post('api/posts/' . $post->id . '/comments', [
            'comment' => ''
        ]);

        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [
                'code' => 422,
                'title' => 'Validation Error',
                'description' => 'Your request is malformed or missing fields',
                'meta' => [
                    'comment' =>  [
                        'The comment field is required.'
                    ]
                ]
            ]
        ]);
        $this->assertArrayHasKey('comment', $response['errors']['meta']);
    }


    public function test_post_are_returned_with_comments()
    {   $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create([
            'user_id' => $user->id
        ]); 
        
        $response = $this->post('api/posts/' . $post->id . '/comments', [
            'comment' => 'test comment'
        ]);
        $comment = Comment::first();
        $response = $this->get('/api/posts/');

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'type' => 'posts',
                    'id' => $post->id,
                    'attributes' => [
                        'body' =>  $post->body,
                        'image' => $post->image,
                        'posted_at' => $post->created_at->diffForHumans(),
                        'comments_count' => 1,
                        'comments' => [
                            [
                                'type' => 'comments',
                                'id' => $comment->id,
                                'attributes' => [
                                    'commented_by' => [
                                        'type' => 'users',
                                        'id' => $comment->user_id,
                                        'attributes' => [
                                            'name' => $user->name
                                        ]
                                    ],
                                    'post_id' => $post->id,
                                    'comment' => $comment->comment,
                                    'comment_at' => $comment->created_at->diffForHumans()
                                ],
                                'links' => [
                                    'self' => url('/api/posts/' . $post->id)
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ]);
    }
}
