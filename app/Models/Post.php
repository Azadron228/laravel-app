<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'slug',
        'user_id',
        'thumbnail',
        'description'
    ];


    /**
     * Attach tags to post.
     *
     * @param array<string> $tags
     */
    public function attachTags(array $tags): void
    {
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate([
                'name' => $tagName,
            ]);

            $this->tags()->syncWithoutDetaching($tag);
        }
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorited()
    {
        return $this->belongsToMany(User::class, 'favorites', 'post_id', 'user_id')
            ->withTimestamps();
    }
}
