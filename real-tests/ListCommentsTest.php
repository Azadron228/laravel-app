<?php

namespace Tests\Feature\Api\Comments;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListCommentsTest extends TestCase
{
    public function testListpostCommentsWithoutAuth(): void
    {
        /** @var post $post */
        $post = Post::factory()
            ->has(Comment::factory()->count(5), 'comments')
            ->create();
        /** @var Comment $comment */
        $comment = $post->comments->first();
        $user = $comment->user;

        $response = $this->getJson("/api/posts/{$post->slug}/comments");

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('comments', 5, fn (AssertableJson $item) =>
                    $item->where('id', $comment->getKey())
                        ->whereAll([
                            'createdAt' => $comment->created_at?->toISOString(),
                            'updatedAt' => $comment->updated_at?->toISOString(),
                            'body' => $comment->body,
                        ])
                        ->has('user', fn (AssertableJson $subItem) =>
                            $subItem->missing('following')
                                ->whereAll([
                                    'username' => $user->username,
                                    'bio' => $user->bio,
                                    'avatar' => $user->avatar,
                                ])->etc()
                        )
                )
            );
    }

    public function testListpostCommentsUnfollowedAuthor(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var post $post */
        $post = post::factory()
            ->has(Comment::factory(), 'comments')
            ->create();

        $response = $this->actingAs($user)
            ->getJson("/api/posts/{$post->slug}/comments");

        $response->assertOk()
            ->assertJsonPath('comments.0.user.following', false);
    }

    public function testListEmptypostComments(): void
    {
        /** @var post $post */
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->slug}/comments");

        $response->assertOk()
            ->assertExactJson(['comments' => []]);
    }

    public function testListCommentsOfNonExistentpost(): void
    {
        $this->getJson('/api/posts/non-existent/comments')
            ->assertNotFound();
    }
}
