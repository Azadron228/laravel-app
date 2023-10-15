<?php

namespace Tests\Feature\Api\Comments;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Tests\TestCase;

class DeleteCommentTest extends TestCase
{
    private Comment $comment;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Comment $comment */
        $comment = Comment::factory()->create();

        $this->comment = $comment;
        $this->post = $comment->post;
    }

    public function testDeletepostComment(): void
    {
        $this->actingAs($this->comment->user)
            ->deleteJson("/api/posts/{$this->post->slug}/comments/{$this->comment->getKey()}")
            ->assertOk();

        $this->assertModelMissing($this->comment);
    }

    public function testDeleteCommentOfNonExistentpost(): void
    {
        $this->assertNotSame($nonExistentSlug = 'non-existent', $this->post->slug);

        $this->actingAs($this->comment->user)
            ->deleteJson("/api/posts/{$nonExistentSlug}/comments/{$this->comment->getKey()}")
            ->assertNotFound();

        $this->assertModelExists($this->comment);
    }

    public function testDeleteForeignpostComment(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson("/api/posts/{$this->post->slug}/comments/{$this->comment->getKey()}")
            ->assertForbidden();

        $this->assertModelExists($this->comment);
    }

    public function testDeleteCommentOfForeignpost(): void
    {
        /** @var post $post */
        $post = Post::factory()->create();

        $this->actingAs($this->comment->user)
            ->deleteJson("/api/posts/{$post->slug}/comments/{$this->comment->getKey()}")
            ->assertNotFound();

        $this->assertModelExists($this->comment);
    }

    public function testDeleteCommentWithoutAuth(): void
    {
        $this->deleteJson("/api/posts/{$this->post->slug}/comments/{$this->comment->getKey()}")
            ->assertUnauthorized();

        $this->assertModelExists($this->comment);
    }

    /**
     * @return array<int|string, array<mixed>>
     */
    public function nonExistentIdProvider(): array
    {
        return [
            'int key' => [123],
            'string key' => ['non-existent'],
        ];
    }
}
