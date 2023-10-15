<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddFavoriteTest extends TestCase
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

    public function testAddPostToFavorites(): void
    {
        /** @var Post $post*/
        $post = Post::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson("/api/posts/{$post->slug}/favorite");
        $response->assertOk()
            ->assertJsonPath('post.favorited', true)
            ->assertJsonPath('post.favoritesCount', 1);

        $this->assertTrue($this->user->favorites->contains($post));

        $repeatedResponse = $this->actingAs($this->user)
            ->postJson("/api/posts/{$post->slug}/favorite");
        $repeatedResponse->assertOk()
            ->assertJsonPath('post.favoritesCount', 1);

        $this->assertDatabaseCount('favorites', 1);
    }

    public function testAddNonExistentPostToFavorites(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/posts/999999/favorite')
            ->assertNotFound();
    }

    public function testAddPostToFavoritesWithoutAuth(): void
    {
        /** @var Post $post */
        $post = Post::factory()->create();

        $this->postJson("/api/posts/{$post->slug}/favorite")
            ->assertUnauthorized();
    }
}
