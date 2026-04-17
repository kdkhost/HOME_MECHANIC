<?php

namespace App\Modules\Blog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt',
        'content', 'cover_image', 'status', 'featured',
        'published_at', 'sort_order',
    ];

    protected $casts = [
        'featured'     => 'boolean',
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function category(): BelongsTo { return $this->belongsTo(BlogCategory::class, 'category_id'); }

    public function scopePublished($q) { return $q->where('status', 'published')->where('published_at', '<=', now()); }
    public function scopeDraft($q)     { return $q->where('status', 'draft'); }
    public function scopeFeatured($q)  { return $q->where('featured', true); }
    public function scopeSearch($q, string $s) {
        return $q->where(fn($x) => $x->where('title', 'like', "%$s%")->orWhere('excerpt', 'like', "%$s%"));
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            if (empty($post->slug)) $post->slug = static::uniqueSlug($post->title);
            if ($post->status === 'published' && !$post->published_at) $post->published_at = now();
        });
        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) $post->slug = static::uniqueSlug($post->title, $post->id);
            if ($post->isDirty('status') && $post->status === 'published' && !$post->published_at) $post->published_at = now();
        });
    }

    public static function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title); $slug = $base; $i = 1;
        while (static::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = "$base-$i"; $i++;
        }
        return $slug;
    }
}
