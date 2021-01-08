<?php

namespace Tests\Feature;

use App\Image;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
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
    public function test_image_can_be_upladed()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user-image.png');

        $response = $this->post('api/users/' . $user->id . '/images', [
            'image' => $file,
            'heigh' => 200,
            'width' => 300,
            'location' => 'cover'
        ])->assertStatus(201);

        Storage::disk('s3')->assertExists('user-images/' . $file->hashName());

        $image = Image::first();
        $this->assertEquals('user-images/' . $file->hashName(), $image->path);
        $this->assertEquals('200', $image->heigh);
        $this->assertEquals('300', $image->width);

        $response->assertJson([
            'data' => [
                'type' => 'images',
                'id' => $image->id,
                'attributes' => [
                    'path' => Config::get('aws.s3_url').$image->path,
                    'width' => $image->width,
                    'heigh' => $image->heigh,
                    'location' => $image->location,
                ],
                'links' => [
                    'self' => url('api/images/')
                ]
            ]
        ]);
    }


    public function test_users_returned_with_their_images()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user-image.png');

        $this->post('api/users/' . $user->id . '/images', [
            'image' => $file,
            'heigh' => 200,
            'width' => 300,
            'location' => 'cover'
        ])->assertStatus(201);

        $this->post('api/users/' . $user->id . '/images', [
            'image' => $file,
            'heigh' => 200,
            'width' => 300,
            'location' => 'profile'
        ])->assertStatus(201);
 
        $response = $this->get('api/users/' . $user->id);
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'attributes' => [
                        'name' => $user->name,
                        'cover_image' => [
                            'type' => 'images',
                            'id' => 1,
                            'attributes' => [],
                            'links' => [
                                'self' => url('api/images/')
                            ]
                        ],
                        'profile_image' => [
                            'type' => 'images',
                            'id' => 2,
                            'attributes' => [],
                            'links' => [
                                'self' => url('api/images/')
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => url('api/users/' . $user->id)
                    ]
                ]
            ]);
    }
}
