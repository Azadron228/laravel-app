<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListCommentTest extends TestCase
{
    public function testListArticleCommentsWithoutAuth(): void
    {
        /** @var Article $article */
        $post = Post::factory()
            ->has(Comment::factory()->count(5), 'comments')
            ->create();
        /** @var Comment $comment */
        $comment = $post->comments->first();

        $response = $this->getJson("/posts/{$post->slug}/comments");

        $response->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'comments',
                    5,
                    fn (AssertableJson $item) => $item->where('id', $comment->getKey())
                        ->whereAll([
                            'created_at' => $comment->created_at?->toISOString(),
                            'updated_at' => $comment->updated_at?->toISOString(),
                            'body' => $comment->body,
                        ])
                        ->has(
                            'author',
                            fn (AssertableJson $subItem) => $subItem->missing('following')
                                ->whereAllType([
                                    'username' => 'string',
                                    'bio' => 'string',
                                ])->etc()
                        )
                )
            );
    }
}
