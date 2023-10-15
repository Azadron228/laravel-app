<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use  Illuminate\Support\Str;

class StorePostRequest extends FormRequest
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
            'title' => ['string', 'max:255'],
            'slug' => ['string', 'max:255', $unique],
            'description' => ['string', 'max:510'],
            'body' => ['string'],
            // 'tags' => 'sometimes|array',
            'tags.*' => 'required|string|max:255',
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
