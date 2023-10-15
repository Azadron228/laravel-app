<?php

namespace Tests\Feature\Api\Article;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function testCreateArticle(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $title = 'Original title';
        $description = $this->faker->paragraph();
        $body = $this->faker->text();
        $tags = ['one', 'two', 'three', 'four', 'five'];

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'post' => [
                    'title' => $title,
                    'slug' => 'different-slug', // must be overwritten with title slug
                    'description' => $description,
                    'body' => $body,
                    'tags' => $tags,
                ],
            ]);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('post', fn (AssertableJson $item) =>
                    $item->where('tags', $tags)
                        ->whereAll([
                            'slug' => 'original-title',
                            'title' => $title,
                            'description' => $description,
                            'body' => $body,
                            'favorited' => false,
                            'favoritesCount' => 0,
                        ])
                        ->whereAllType([
                            'createdAt' => 'string',
                            'updatedAt' => 'string',
                        ])
                        ->has('user', fn (AssertableJson $subItem) =>
                            $subItem->whereAll([
                                'username' => $user->username,
                                'bio' => $user->bio,
                                'following' => false,
                            ])->etc()
                        )
                )
            );
    }

    public function testCreateArticleEmptyTags(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'post' => [
                    'title' => $this->faker->sentence(4),
                    'description' => $this->faker->paragraph(),
                    'body' => $this->faker->text(),
                    'tags' => [],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('post.tags', []);
    }

    public function testCreateArticleValidationUnique(): void
    {
        /** @var post $post */
        $post = Post::factory()->create();

        $response = $this->actingAs($post->user)
            ->postJson('/api/posts', [
                'post' => [
                    'title' => $post->title,
                    'description' => $this->faker->paragraph(),
                    'body' => $this->faker->text(),
                ],
            ]);

        $response->assertUnprocessable()
            ->assertInvalid('slug');
    }

    public function testCreateArticleWithoutAuth(): void
    {
        $response = $this->postJson('/api/posts', [
            'post' => [
                'title' => $this->faker->sentence(4),
                'description' => $this->faker->paragraph(),
                'body' => $this->faker->text(),
            ],
        ]);

        $response->assertUnauthorized();
    }
}
