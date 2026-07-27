<?php

namespace Tests\Feature\Api;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UnsubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_unsubscribe_url_successfully_unsubscribes_user(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber@example.com',
            'status' => SubscriptionStatus::Active,
            'source' => 'footer',
            'subscribed_at' => now(),
        ]);

        $signedBackendUrl = URL::temporarySignedRoute(
            'api.newsletter.unsubscribe',
            now()->addDays(30),
            ['email' => $subscription->email]
        );

        $parsed = parse_url($signedBackendUrl);
        parse_str($parsed['query'], $queryParams);

        $response = $this->postJson(route('api.newsletter.unsubscribe', $queryParams));

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'subscriber@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_invalid_signature_returns_forbidden(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $response = $this->postJson(route('api.newsletter.unsubscribe'), [
            'email' => $subscription->email,
            'signature' => 'invalid-signature-hash',
        ]);

        $response->assertForbidden()
            ->assertJson([
                'message' => 'The unsubscribe link is invalid or has expired.',
            ]);
    }

    public function test_expired_signature_returns_forbidden(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $expiredUrl = URL::temporarySignedRoute(
            'api.newsletter.unsubscribe',
            now()->subMinutes(10),
            ['email' => $subscription->email]
        );

        $parsed = parse_url($expiredUrl);
        parse_str($parsed['query'], $queryParams);

        $response = $this->postJson(route('api.newsletter.unsubscribe', $queryParams));

        $response->assertForbidden();
    }
}
