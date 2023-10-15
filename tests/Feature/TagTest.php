<?php

namespace Tests\Feature;

use App\Models\Tag;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TagTest extends TestCase
{
    public function testReturnsTagsList(): void
    {
        $tags = Tag::factory()->count(5)->create();
        $response = $this->getJson('api/tags');
        $response->dump();

         $response->assertJson(fn (AssertableJson $json) =>
    $json->has('tags')
        ->whereType('tags.0', 'string')
);
    }
}
