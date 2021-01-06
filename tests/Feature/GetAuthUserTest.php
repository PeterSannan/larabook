<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAuthUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_authenticated_user_can_be_fetched()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $response = $this->get('/api/users/auth-user');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => $user->id,
                    'attributes' => [
                        'name' => $user->name
                    ],
                    'links' => [
                        'self' => url('api/users/'.$user->id)
                    ]
                ]
            ]);
    }
}
