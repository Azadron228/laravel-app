<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteFavoriteTest extends TestCase
{
        private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testRemovepostFromFavorites(): void
    {
        /** @var post $post */
        $post = Post::factory()
            ->hasAttached($this->user, [], 'favorited')
            ->create();
        // dd($post);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/posts/{$post->slug}/favorite");
        // $response->dump();
        $response->assertOk()
            ->assertJsonPath('post.favorited', false)
            ->assertJsonPath('post.favoritesCount', 0);

        $this->assertTrue($this->user->favorites->doesntContain($post));

        $this->actingAs($this->user)
            ->deleteJson("/api/posts/{$post->slug}/favorite")
            ->assertOk();
    }

    public function testRemoveNonExistentpostFromFavorites(): void
    {
        $this->actingAs($this->user)
            ->deleteJson('/api/posts/non-existent/favorite')
            ->assertNotFound();
    }

    public function testRemovepostFromFavoritesWithoutAuth(): void
    {
        /** @var post $post */
        $post = Post::factory()->create();

        $this->deleteJson("/api/posts/{$post->slug}/favorite")
            ->assertUnauthorized();
    }
}
