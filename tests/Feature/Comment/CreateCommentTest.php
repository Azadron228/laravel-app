<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->postJson("/jojo/{$this->post->slug}/comments", [
                'comment' => [
                    'body' => $message,
                ],
            ]);
        dd($response);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('comment', fn (AssertableJson $comment) =>
                    $comment->where('body', $message)
                        ->whereAllType([
                            'id' => 'integer',
                            'created_at' => 'string',
                            'updated_at' => 'string',
                        ])
                        ->has('author', fn (AssertableJson $author) =>
                            $author->whereAll([
                                'username' => $this->user->username,
                                'bio' => $this->user->bio,
                                // 'image' => $this->user->image,
                                // 'following' => false,
                            ])->etc()
                        )
                )
            );
    }
}
