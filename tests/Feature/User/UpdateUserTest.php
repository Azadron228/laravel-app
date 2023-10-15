<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Storage;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testUpdateUser(): void
    {
        $username = 'new.username';
        $email = 'newEmail@example.com';
        $bio = 'New bio information.';

        $this->actingAs($this->user)
            ->putJson('/api/user', ['user' => ['username' => $username]])
            ->assertOk();
        $this->actingAs($this->user)
            ->putJson('/api/user', ['user' => ['email' => $email]])
            ->assertOk();
        $response = $this->actingAs($this->user)
            ->putJson('/api/user', ['user' => ['bio' => $bio]]);

        $response->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'user',
                    fn (AssertableJson $item) => $item
                        ->whereAll([
                            'username' => $username,
                            'email' => $email,
                            'bio' => $bio,
                        ])->etc()
                )
            );
    }


    public function testUpdateUserAvatar(): void
    {
        Storage::fake('avatars');

        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->user)
            ->post('/api/user', ['avatar' => $avatar]);

                $response->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'user',
                    fn (AssertableJson $item) => $item
                        ->whereAll([
                            'avatar'=> '/storage/avatars/' . $avatar->hashName(),
                        ])->etc()
                )
            );


        Storage::disk('public')->assertExists('avatars/' . $avatar->hashName());
    }
 }
