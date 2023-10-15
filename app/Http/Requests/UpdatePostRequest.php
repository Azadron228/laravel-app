<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Post;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $input = $this->input();
        $title = Arr::get($input, 'post.title');

        if (is_string($title)) {
            Arr::set($input, 'post.slug', Str::slug($title));
        } else {
            Arr::forget($input, 'post.slug');
        }

        $this->merge($input);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $post = Post::whereSlug($this->route('slug'))
            ->first();

        $unique = Rule::unique('posts', 'slug');
        if ($post !== null) {
            $unique->ignoreModel($post);
        }

        return [
            'title' => 'sometimes|string|max:255',
            'slug' => ['string', 'max:255', $unique],
            'body' => 'sometimes|string',
            'description' => 'sometimes|string',
            'thumbnail' => 'sometimes|image|mimes:jpeg,jpg,gif,png|max:20480',
        ];
    }

    /**
     * @return array<mixed>
     */
    public function validationData()
    {
        return Arr::wrap($this->input('post'));
    }
}
