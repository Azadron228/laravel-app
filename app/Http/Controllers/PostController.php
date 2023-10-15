<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();

        if ($request->has('tags')) {
            $tagName = $request->input('tags');

            $query->whereHas(
                'tags',
                fn (Builder $builder) => $builder->where('name', $tagName)
            );
        }

        if ($request->has('author')) {
            $tagName = $request->input('author');

            $user = User::where('username', $tagName)->first();
            $query = $user->posts();
        }

        if ($request->has('favorited')) {
            $favoritedName = $request->input('favorited');

            $user = User::where('username', $favoritedName)->first();

            if ($user) {
                // If the user exists, filter posts that are favorited by the user.
                $query->whereIn('id', $user->favorites->pluck('id'));
            } else {
                // Handle the case where the favorited user doesn't exist.
                return response()->json(['message' => 'Favorited user not found'], 404);
            }
        }
        $posts = $query->paginate(10);

        return new PostCollection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $user = $request->user();
        $postData = $request->input('post');
        $tags = $postData['tags'];

        $validatedData = $request->validated();
        $validatedData['user_id'] = $user->id;
        $post = Post::create($validatedData);
        if (is_array($tags)) {
            $post->attachTags($tags);
        }

        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post, string $slug)
    {
        $post = Post::where('slug', $slug)->first();
        $this->authorize('update', $post);
        $validatedData = $request->validated();
        $post->update($validatedData);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $post = Post::whereSlug($slug)
            ->firstOrFail();

        $this->authorize('delete', $post);

        $post->delete(); // cascade

        return response()->json([
            'message' => trans('models.article.deleted'),
        ]);
    }

    /** Fovorites Logic **/
    public function favorite(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $user = auth()->user();
        $user->favorites()->syncWithoutDetaching($post->id);

        return new PostResource($post);
    }

    public function unfavorite(Request $request, $slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $user = auth()->user();
        $user->favorites()->detach($post->id);

        return new PostResource($post);
    }
}
