<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

    /**
     * Get all possible signing keys including previous keys.
     *
     * @return list<string>
     */
    protected static function getSigningKeys(): array
    {
        $keys = [];
        $currentKey = config('app.key');
        if (! empty($currentKey)) {
            $keys[] = (string) $currentKey;
        }

        $previousKeys = config('app.previous_keys', []);
        if (is_array($previousKeys)) {
            foreach ($previousKeys as $key) {
                if (! empty($key)) {
                    $keys[] = (string) $key;
                }
            }
        }

        return $keys;
    }

    /**
     * Validate whether an unsubscribe link signature is valid and has not expired.
     */
    public static function hasValidUnsubscribeSignature(string $email, mixed $expires, ?string $signature, ?Request $request = null): bool
    {
        if (empty($signature) || empty($expires)) {
            return false;
        }

        if (! is_numeric($expires) || (int) $expires < now()->timestamp) {
            return false;
        }

        if ($request !== null) {
            if ($request->hasValidSignature()) {
                return true;
            }
            if ($request->hasValidSignature(false)) {
                return true;
            }
        }

        $keys = self::getSigningKeys();
        if (empty($keys)) {
            return false;
        }

        $backendUrl = route('api.newsletter.unsubscribe', [
            'email' => $email,
            'expires' => $expires,
        ]);

        $relativeBackendUrl = url('/api/unsubscribe', [
            'email' => $email,
            'expires' => $expires,
        ], false);

        $frontendBase = rtrim(config('app.frontend_url', 'https://sealtech.co.tz'), '/');
        $frontendUrlWithRouteQuery = $frontendBase.'/unsubscribe?'.parse_url($backendUrl, PHP_URL_QUERY);
        $frontendUrlHttpBuildQuery = $frontendBase.'/unsubscribe?'.http_build_query([
            'email' => $email,
            'expires' => $expires,
        ]);

        $candidates = array_unique([
            $backendUrl,
            $relativeBackendUrl,
            $frontendUrlWithRouteQuery,
            $frontendUrlHttpBuildQuery,
        ]);

        foreach ($keys as $key) {
            foreach ($candidates as $candidate) {
                $expected = hash_hmac('sha256', $candidate, $key);
                if (hash_equals($expected, (string) $signature)) {
                    return true;
                }
            }
        }

        return false;
    }
}
