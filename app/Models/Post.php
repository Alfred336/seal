<?php

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string|null $excerpt
 * @property string|null $content
 * @property int|null $author_id
 * @property int|null $category_id
 * @property string|null $image_path
 * @property string|null $image_alt
 * @property string|null $image_gradient
 * @property string|null $image_icon
 * @property string|null $read_time
 * @property bool $featured
 * @property PostStatus $status
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'author_id',
        'category_id',
        'image_path',
        'image_alt',
        'image_gradient',
        'image_icon',
        'read_time',
        'featured',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $post) {
            if (empty($post->image_gradient)) {
                $post->image_gradient = 'linear-gradient(135deg, #7F1D1D 0%, #DC2626 60%, #F97316 100%)';
            }
            if (empty($post->image_icon)) {
                $post->image_icon = '<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><path d="M20 24v-4a8 8 0 1 1 16 0v4" stroke="rgba(255,255,255,0.7)" stroke-width="2"/><rect x="16" y="24" width="24" height="18" rx="4" fill="rgba(255,255,255,0.22)"/></svg>';
            }
        });
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Draft);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->author_id === $user->id;
    }
}
