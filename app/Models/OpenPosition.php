<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property string $location
 * @property string|null $tech_stack
 * @property string $description
 * @property PostStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class OpenPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'location',
        'tech_stack',
        'description',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
        ];
    }

    /**
     * Scope a query to only include published open positions.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published);
    }
}
