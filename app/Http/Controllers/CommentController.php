<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $slug)
    {
        $post = Post::whereSlug($slug)
            ->firstOrFail();

        return new CommentCollection($post->comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, string $slug)
    {
        $post = Post::whereSlug($slug)
            ->firstOrFail();
        $user = $request->user();

        $comment = Comment::create([
            'post_id' => $post->getKey(),
            'user_id' => $user->getKey(),
            'body' => $request->input('comment.body'),
        ]);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug, $id)
    {
        // $comment = Comment::findOrFail($id);
        //
        // $this->authorize('delete', $comment);
        // $comment->delete();
        // return response()->json(['message' => 'Comment deleted successfully']);
        //


        $post = Post::whereSlug($slug)
            ->firstOrFail();

        $comment = $post->comments()
            ->findOrFail((int) $id);

        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => trans('models.comment.deleted'),
        ]);
    }
}
