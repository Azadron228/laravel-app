<?php

namespace Tests\Feature\Api\post;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Post::factory()->count(30)->create();
    }

        public function testListPost(): void
    {
        $response = $this->get('api/posts');
        $response->dump();

        $response->assertJson(
            fn (AssertableJson $json) => $json->has('meta')->has('links')->has(
                'data',
                fn (AssertableJson $data) => $data->each(
                    fn (AssertableJson $item) => $item->whereAllType([
                        'title' => 'string',
                        'description' => 'string',
                        'body' => 'string',
                        'tags' => 'array',
                    ])->has(
                        'user',
                        fn (AssertableJson $subItem) => $subItem->whereAllType([
                            'id' => 'integer',
                            'email' => 'string',
                            'created_at' => 'string',
                            'updated_at' => 'string',
                            'username' => 'string',
                            'bio' => 'string',
                            'avatar' => 'string',
                            'isFollowing' => 'boolean',
                        ])->etc()
                    )->etc()
                )->etc()
            )
        );
    }

    // public function testListpostsByTag(): void
    // {
    //     // dummy posts shouldn't be returned
    //     Post::factory()
    //         ->has(Tag::factory()->count(3))
    //         ->count(20)
    //         ->create();
    //
    //     /** @var Tag $tag */
    //     $tag = Tag::factory()
    //         ->has(post::factory()->count(10), 'posts')
    //         ->create();
    //
    //     $response = $this->getJson("/api/posts?tag={$tag->name}");
    //
    //     $response->assertOk()
    //         ->assertJsonPath('postsCount', 10)
    //         ->assertJsonCount(10, 'posts');
    //
    //     // verify has tag
    //     foreach ($response['posts'] as $post) {
    //         $this->assertContains(
    //             $tag->name, Arr::get($post, 'tagList'),
    //             "post must have tag {$tag->name}"
    //         );
    //     }
    // }
    //
    // public function testListpostsByAuthor(): void
    // {
    //     /** @var User $author */
    //     $author = User::factory()
    //         ->has(post::factory()->count(5), 'posts')
    //         ->create();
    //
    //     $response = $this->getJson("/api/posts?author={$author->username}");
    //
    //     $response->assertOk()
    //         ->assertJsonPath('postsCount', 5)
    //         ->assertJsonCount(5, 'posts');
    //
    //     // verify same author
    //     foreach ($response['posts'] as $post) {
    //         $this->assertSame(
    //             $author->username,
    //             Arr::get($post, 'author.username'),
    //             "Author must be {$author->username}."
    //         );
    //     }
    // }
    //
    // public function testListpostsByFavored(): void
    // {
    //     /** @var User $user */
    //     $user = User::factory()
    //         ->has(post::factory()->count(15), 'favorites')
    //         ->create();
    //
    //     $response = $this->getJson("/api/posts?favorited={$user->username}");
    //
    //     $response->assertOk()
    //         ->assertJsonPath('postsCount', 15)
    //         ->assertJsonCount(15, 'posts');
    //
    //     // verify favored
    //     foreach ($response['posts'] as $post) {
    //         $this->assertSame(
    //             1, Arr::get($post, 'favoritesCount'),
    //             "post must be favored by {$user->username}."
    //         );
    //     }
    // }
    //
    // public function testpostFeedLimit(): void
    // {
    //     $response = $this->getJson('/api/posts?limit=25');
    //
    //     $response->assertOk()
    //         ->assertJsonPath('postsCount', 25)
    //         ->assertJsonCount(25, 'posts');
    // }
    //
    // public function testpostFeedOffset(): void
    // {
    //     $response = $this->getJson('/api/posts?offset=20');
    //
    //     $response->assertOk()
    //         ->assertJsonPath('postsCount', 10)
    //         ->assertJsonCount(10, 'posts');
    // }
    //
    // /**
    //  * @return array<int|string, array<mixed>>
    //  */
    // public function queryProvider(): array
    // {
    //     $errors = ['limit', 'offset'];
    //
    //     return [
    //         'not integer' => [['limit' => 'string', 'offset' => 0.123], $errors],
    //         'less than zero' => [['limit' => -123, 'offset' => -321], $errors],
    //         'not strings' => [[
    //             'tag' => 123,
    //             'author' => [],
    //             'favorited' => null,
    //         ], ['tag', 'author', 'favorited']],
    //     ];
    // }
}
