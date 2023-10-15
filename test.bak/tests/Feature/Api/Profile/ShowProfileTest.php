<?php

namespace Tests\Feature\Api\Profile;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ShowProfileTest extends TestCase
{
    private User $profile;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $this->profile = $user;
    }

    public function testShowProfileWithoutAuth(): void
    {
        /** @var User $profile */
        $profile = User::factory()->create();

        $response = $this->getJson("/api/profiles/{$profile->username}");

        $response->assertOk()
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'profile',
                    fn (AssertableJson $item) =>
                    $item->whereAllType([
                        'username' => 'string',
                        'email' => 'string',
                        'bio' => 'string',
                    ])->etc()
                )
            );
    }

    public function testShowUnfollowedProfile(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/profiles/{$this->profile->username}");

        $response->assertOk()
            ->assertJsonPath('profile.following', false);
    }

    public function testShowFollowedProfile(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->hasAttached($this->profile, [], 'followers')
            ->create();

        $response = $this->actingAs($user)
            ->getJson("/api/profiles/{$this->profile->username}");

        $response->assertOk()
            ->assertJsonPath('profile.following', true);
    }
}
