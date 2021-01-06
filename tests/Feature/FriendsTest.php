<?php

namespace Tests\Feature;

use App\Friend;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FriendsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_uset_can_friend_request()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $response = $this->post('/api/friend-request', [
            'friend_id' => $another_user->id
        ])->assertStatus(200);

        $friend = Friend::first();
        $this->assertNotNull($friend);
        $this->assertEquals($friend->user_id, auth()->id());
        $this->assertEquals($friend->friend_id, $another_user->id);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'id' => $friend->id,
                'attributes' => [
                    'confirmed_at' => null
                ],
                'links' => [
                    'self' => '/api/users/' . $friend->friend_id
                ]
            ]
        ]);
    }

    public function test_only_valid_friends_can_be_requested()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/friend-request', [
            'friend_id' => 123
        ])->assertStatus(422);

        $friend = Friend::first();
        $this->assertNull($friend);

        $response->assertJson([
            'errors' => [
                'code' => 422,
                'title' => 'Validation Error',
                'description' => 'Your request is malformed or missing fields'
            ]
        ]);
    }

    public function test_user_can_ignore_friend_request()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $another_user->id
        ])->assertStatus(200);

        $this->actingAs($another_user, 'api');

        $friend = Friend::first();

        $response = $this->delete('api/friend-request/' . $friend->id, [
            'state' => 0
        ])->assertStatus(204);

        $this->assertNull(Friend::first()); 
    }

    public function test_only_receipient_can_ignore_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $another_user->id
        ])->assertStatus(200);

        $this->actingAs(factory(User::class)->create(), 'api');

        $friend = Friend::first();

        $response = $this->delete('api/friend-request/' . $friend->id, [
            'state' => 0
        ])->assertJson([
            'errors' => [
                'code' => 403,
                "title" => 'Permission denied',
                "description" => "You don't have permission to access the requested resource not found",
            ]
        ]);

        $this->assertNotNull($friend->fresh());

    }

    public function test_user_can_accept_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $another_user->id
        ])->assertStatus(200);

        $this->actingAs($another_user, 'api');

        $friend = Friend::first();

        $response = $this->put('api/friend-request/' . $friend->id, [
            'state' => 1
        ]);
        $this->assertNotNull($friend->fresh()->confirmed_at);
        $this->assertEquals($friend->fresh()->state, 1);

        $response->assertJson([
            'data' => [
                'type' => 'friend-request',
                'id' => $friend->id,
                'attributes' => [
                    'confirmed_at' => $friend->fresh()->confirmed_at->diffForHumans()
                ],
                'links' => [
                    'self' => '/api/users/' . $friend->friend_id
                ]
            ]
        ]);
    }

    public function test_only_valid_friend_request_can_be_accepted()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $this->actingAs($another_user, 'api');

        $response = $this->put('api/friend-request/123', [
            'state' => 1
        ]);

        $response->assertJson([
            'errors' => [
                'code' => 404,
                "title" => 'Resource not found',
                "description" => 'The requested resource not found',
            ]
        ]);
    }

    

    public function test_only_the_receipient_can_accept_the_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $another_user->id
        ])->assertStatus(200);

        $friend = Friend::first();

        $this->actingAs($third_user = factory(User::class)->create(), 'api');
        $response = $this->put('api/friend-request/' . $friend->id, [
            'state' => 1
        ]);
        $this->assertNull($friend->fresh()->confirmed_at);
        $this->assertNull($friend->fresh()->state);

        $response->assertJson([
            'errors' => [
                'code' => 403,
                "title" => 'Permission denied',
                "description" => "You don't have permission to access the requested resource not found",
            ]
        ]);
    }


    public function test_state_is_required_accept_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $this->post('/api/friend-request', [
            'friend_id' => $another_user->id
        ])->assertStatus(200);

        $this->actingAs($another_user, 'api');

        $friend = Friend::first();

        $response = $this->put('api/friend-request/' . $friend->id, [
            'state' => ''
        ])->assertStatus(422);
        $this->assertArrayHasKey('state', $response['errors']['meta']);
    }

    public function test_friendship_can_be_retreived_when_we_visit_a_profile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $response = $this->get('api/users/' . $another_user->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'name' => $another_user->name,
                        'friendship' => null
                    ],
                    'links' => [
                        'self' => url('api/users/' . $another_user->id)
                    ]
                ]
            ]);

        $friend = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $another_user->id,
            'confirmed_at' => Carbon::now(),
            'state' => 1
        ]);
        $response = $this->get('api/users/' . $another_user->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'name' => $another_user->name,
                        'friendship' => [
                            'type' => 'friend-request',
                            'id' => $friend->id,
                            'attributes' => [
                                'confirmed_at' => $friend->confirmed_at->diffForHumans()
                            ],
                        ]
                    ]
                ]
            ]);
    }

    public function test_friendship_can_be_retreived_when_we_visit_a_profile_inverse()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $another_user = factory(User::class)->create();

        $friend = Friend::create([
            'friend_id' => $user->id,
            'user_id' => $another_user->id,
            'confirmed_at' => Carbon::now(),
            'state' => 1
        ]);
        $response = $this->get('api/users/' . $another_user->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'name' => $another_user->name,
                        'friendship' => [
                            'type' => 'friend-request',
                            'id' => $friend->id,
                            'attributes' => [
                                'confirmed_at' => $friend->confirmed_at->diffForHumans()
                            ],
                        ]
                    ]
                ]
            ]);
    }
}
