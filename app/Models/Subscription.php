<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * @property int $id
 * @property SubscriptionStatus $status
 * @property Carbon $subscribed_at
 * @property Carbon|null $unsubscribed_at
 */
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'status',
        'source',
        'subscribed_at',
        'unsubscribed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<Subscription>  $query
     * @return Builder<Subscription>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SubscriptionStatus::Active);
    }

    /**
     * Generate a signed unsubscribe URL pointing to the frontend domain.
     */
    public static function generateUnsubscribeUrl(string $email, int $ttlDays = 30): string
    {
        $signedBackendUrl = URL::temporarySignedRoute(
            'api.newsletter.unsubscribe',
            now()->addDays($ttlDays),
            ['email' => $email]
        );

        $parsed = parse_url($signedBackendUrl);
        $frontendUrl = rtrim(config('app.frontend_url', 'https://sealtech.co.tz'), '/');

        return $frontendUrl.'/unsubscribe?'.($parsed['query'] ?? '');
    }
}
