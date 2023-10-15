<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'slug' => fn (array $attrs) => Str::slug($attrs['title']),
            'title' => $this->faker->unique()->sentence(4),
            'description' => $this->faker->paragraph(),
            'body' => $this->faker->text(),
        ];
    }
}
