<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use WithFaker;

    public function testRegisterUser(): void
    {
        $username = $this->faker->userName();
        $email = $this->faker->safeEmail();

        $response = $this->postJson('/api/users/register', [
            'user' => [
                'username' => $username,
                'email' => $email,
                'password' => $this->faker->password(8),
            ],
        ]);

        $response->assertCreated()
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'user',
                    fn (AssertableJson $item) =>
                    $item->whereAll([
                        'username' => $username,
                        'email' => $email,
                        'bio' => null,
                        'avatar' => null,
                    ])->etc()
                )
            );

        $user = User::where('email', $email)->first();
        $this->assertAuthenticatedAs($user);
    }

    public function testRegisterUserValidationUnique(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->postJson('/api/users/register', [
            'user' => [
                'username' => $user->username,
                'email' => $user->email,
                'password' => $this->faker->password(8),
            ],
        ]);

        $response->assertUnprocessable()
            ->assertInvalid(['username', 'email']);
    }

    /**
     * @return array<int|string, array<mixed>>
     */
    public function userProvider(): array
    {
        $errors = ['username', 'email', 'password'];

        return [
            'required' => [[], $errors],
            'not strings' => [[
                'user' => [
                    'username' => 123,
                    'email' => [],
                    'password' => null,
                ],
            ], $errors],
            'empty strings' => [[
                'user' => [
                    'username' => '',
                    'email' => '',
                    'password' => '',
                ],
            ], $errors],
            'bad username' => [['user' => ['username' => 'user n@me']], 'username'],
            'not email' => [['user' => ['email' => 'not an email']], 'email'],
            'small password' => [['user' => ['password' => 'small']], 'password'],
        ];
    }
}
