<?php

namespace Tests\Feature\Api;

use App\Enums\Permission;
use App\Enums\Role;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\User;
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

        $queryString = parse_url($signedBackendUrl, PHP_URL_QUERY);
        parse_str(is_string($queryString) ? $queryString : '', $queryParams);

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

        $queryString = parse_url($expiredUrl, PHP_URL_QUERY);
        parse_str(is_string($queryString) ? $queryString : '', $queryParams);

        $response = $this->postJson(route('api.newsletter.unsubscribe', $queryParams));

        $response->assertForbidden()
            ->assertJson([
                'message' => 'The unsubscribe link is invalid or has expired.',
            ]);
    }

    public function test_signed_unsubscribe_via_post_body_successfully_unsubscribes_user(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber2@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $signedBackendUrl = URL::temporarySignedRoute(
            'api.newsletter.unsubscribe',
            now()->addDays(30),
            ['email' => $subscription->email]
        );

        $queryString = parse_url($signedBackendUrl, PHP_URL_QUERY);
        parse_str(is_string($queryString) ? $queryString : '', $params);

        $response = $this->postJson(route('api.newsletter.unsubscribe'), [
            'email' => $params['email'],
            'expires' => $params['expires'],
            'signature' => $params['signature'],
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'subscriber2@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_signed_unsubscribe_via_get_request_successfully_unsubscribes_user(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber3@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $signedBackendUrl = URL::temporarySignedRoute(
            'api.newsletter.unsubscribe',
            now()->addDays(30),
            ['email' => $subscription->email]
        );

        $queryString = parse_url($signedBackendUrl, PHP_URL_QUERY);
        parse_str(is_string($queryString) ? $queryString : '', $queryParams);

        $response = $this->getJson(route('api.newsletter.unsubscribe', $queryParams));

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'subscriber3@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_signed_unsubscribe_url_pointing_to_frontend_domain_is_valid(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber4@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $frontendUrl = Subscription::generateUnsubscribeUrl($subscription->email);
        $this->assertStringContainsString('sealtech.co.tz/unsubscribe', $frontendUrl);

        $queryString = parse_url($frontendUrl, PHP_URL_QUERY);
        parse_str(is_string($queryString) ? $queryString : '', $params);

        $response = $this->postJson(route('api.newsletter.unsubscribe'), $params);

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'subscriber4@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_missing_signature_without_auth_returns_forbidden(): void
    {
        $subscription = Subscription::create([
            'email' => 'subscriber5@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $response = $this->postJson(route('api.newsletter.unsubscribe'), [
            'email' => $subscription->email,
        ]);

        $response->assertForbidden()
            ->assertJson([
                'message' => 'The unsubscribe link is invalid or has expired.',
            ]);
    }

    public function test_authenticated_user_can_unsubscribe_own_email_without_signature(): void
    {
        $user = User::factory()->create([
            'email' => 'authuser@example.com',
        ]);

        Subscription::create([
            'email' => 'authuser@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $token = $user->createToken('test-app-token')->plainTextToken;

        // Call without signature, passing email
        $response = $this->withToken($token)->postJson(route('api.newsletter.unsubscribe'), [
            'email' => 'authuser@example.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'authuser@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_authenticated_user_can_unsubscribe_without_explicit_email_payload(): void
    {
        $user = User::factory()->create([
            'email' => 'authuser2@example.com',
        ]);

        Subscription::create([
            'email' => 'authuser2@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $token = $user->createToken('test-app-token')->plainTextToken;

        // Call with no email body, defaults to authenticated user's email
        $response = $this->withToken($token)->postJson(route('api.newsletter.unsubscribe'));

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'authuser2@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_authenticated_admin_can_unsubscribe_any_email_without_signature(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@sealtech.test',
        ]);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => Role::Admin->value]);
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => Permission::SubscriptionsManage->value]);
        $admin->givePermissionTo($permission);

        Subscription::create([
            'email' => 'someuser@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $token = $admin->createToken('Production API')->plainTextToken;

        $response = $this->withToken($token)->postJson(route('api.newsletter.unsubscribe'), [
            'email' => 'someuser@example.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'You have been successfully unsubscribed from our mailing list.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'someuser@example.com',
            'status' => SubscriptionStatus::Unsubscribed->value,
        ]);
    }

    public function test_authenticated_non_admin_cannot_unsubscribe_other_email_without_signature(): void
    {
        $user = User::factory()->create([
            'email' => 'regularuser@example.com',
        ]);

        Subscription::create([
            'email' => 'victim@example.com',
            'status' => SubscriptionStatus::Active,
            'subscribed_at' => now(),
        ]);

        $token = $user->createToken('test-app-token')->plainTextToken;

        $response = $this->withToken($token)->postJson(route('api.newsletter.unsubscribe'), [
            'email' => 'victim@example.com',
        ]);

        $response->assertForbidden()
            ->assertJson([
                'message' => 'The unsubscribe link is invalid or has expired.',
            ]);

        $this->assertDatabaseHas('subscriptions', [
            'email' => 'victim@example.com',
            'status' => SubscriptionStatus::Active->value,
        ]);
    }

    public function test_unsubscribing_nonexistent_email_returns_not_found(): void
    {
        $user = User::factory()->create([
            'email' => 'notsubscribed@example.com',
        ]);
        $token = $user->createToken('test-app-token')->plainTextToken;

        $response = $this->withToken($token)->postJson(route('api.newsletter.unsubscribe'));

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Subscription record not found.',
            ]);
    }
}
