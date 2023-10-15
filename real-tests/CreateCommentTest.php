<?php

namespace Tests\Feature\Api\Comments;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateCommentTest extends TestCase
{
    use WithFaker;

    private Post $post;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var post $post */
        $post = Post::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();

        $this->post = $post;
        $this->user = $user;
    }

    public function testCreateCommentForPost(): void
    {
        $message = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->postJson("/api/posts/{$this->post->slug}/comments", [
                'comment' => [
                    'body' => $message,
                ],
            ]);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('comment', fn (AssertableJson $comment) =>
                    $comment->where('body', $message)
                        ->whereAllType([
                            'id' => 'integer',
                            'createdAt' => 'string',
                            'updatedAt' => 'string',
                        ])
                        ->has('user', fn (AssertableJson $user) =>
                            $user->whereAll([
                                'username' => $this->user->username,
                                'bio' => $this->user->bio,
                                'avatar' => $this->user->avatar,
                                'following' => false,
                            ])->etc()
                        )
                )
            );
    }

    public function testCreateCommentForNonExistentpost(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/posts/non-existent/comments", [
                'comment' => [
                    'body' => $this->faker->sentence(),
                ],
            ]);

        $response->assertNotFound();
    }

    public function testCreateCommentWithoutAuth(): void
    {
        $response = $this->postJson("/api/posts/{$this->post->slug}/comments", [
            'comment' => [
                'body' => $this->faker->sentence(),
            ],
        ]);

        $response->assertUnauthorized();
    }
}
