<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'author',
        'published_at'
    ];

    protected $attributes = [
        'status' => PostStatus::DRAFT,
        'published_at' => null
    ];

    protected static function booted()
    {

        // Hook to set the published_at date when the status becomes "published" for the first time.
        static::updating(function ($post) {

            if ($post->status === PostStatus::PUBLISHED && $post->published_at === null) {

                $post->published_at = now();
            }
        });
    }

    /**
     * Scope to order by create_at field ASC
     *
     * @param $query
     * @return mixed
     */
    public function scopeCreatedByAsc($query)
    {

        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope to hide any post in draft status
     *
     * @param $query
     * @return mixed
     */
    public function scopeHideDrafts($query)
    {

        return $query->where('status', PostStatus::PUBLISHED);
    }
}
