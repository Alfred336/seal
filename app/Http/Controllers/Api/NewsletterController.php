<?php

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Enums\SubscriptionStatus;
use App\Http\Requests\Api\NewsletterRequest;
use App\Models\Subscription;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends ApiController
{
    public function store(NewsletterRequest $request): JsonResponse
    {
        $subscription = Subscription::where('email', $request->input('email'))->first();

        if ($subscription) {
            if ($subscription->status === SubscriptionStatus::Active) {
                return response()->json(['message' => 'You are already subscribed.'], 409);
            }

            $subscription->update([
                'status' => SubscriptionStatus::Active,
                'source' => $request->input('source'),
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ]);

            return response()->json(['message' => 'Welcome back! You have been re-subscribed.'], 200);
        }

        Subscription::create([
            'email' => $request->input('email'),
            'source' => $request->input('source'),
        ]);

        return response()->json(['message' => 'Thank you for subscribing!'], 201);
    }

    /**
     * Handle email unsubscribe request using signed URL parameters or authenticated app/user.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?? $request->user();
        $isAuthenticated = $user !== null;

        $email = $request->input('email', $request->query('email'));
        $expires = $request->input('expires', $request->query('expires'));
        $signature = $request->input('signature', $request->query('signature'));

        if ($isAuthenticated) {
            if (! $email && ! empty($user->email)) {
                $email = $user->email;
            }

            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'The email field is required and must be a valid email address.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $isOwnEmail = strcasecmp((string) $user->email, (string) $email) === 0;
            $canManageSubscriptions = $user instanceof Authorizable && $user->can(Permission::SubscriptionsManage->value);
            $hasValidSignature = Subscription::hasValidUnsubscribeSignature((string) $email, $expires, $signature, $request);

            if (! $isOwnEmail && ! $canManageSubscriptions && ! $hasValidSignature) {
                return response()->json([
                    'message' => 'The unsubscribe link is invalid or has expired.',
                ], 403);
            }
        } else {
            if (! $email || ! Subscription::hasValidUnsubscribeSignature((string) $email, $expires, $signature, $request)) {
                return response()->json([
                    'message' => 'The unsubscribe link is invalid or has expired.',
                ], 403);
            }
        }

        $subscription = Subscription::where('email', $email)->first();

        if (! $subscription) {
            return response()->json([
                'message' => 'Subscription record not found.',
            ], 404);
        }

        if ($subscription->status !== SubscriptionStatus::Unsubscribed) {
            $subscription->update([
                'status' => SubscriptionStatus::Unsubscribed,
                'unsubscribed_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'You have been successfully unsubscribed from our mailing list.',
        ], 200);
    }
}
