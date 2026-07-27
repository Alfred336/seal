<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\Api\NewsletterRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Handle email unsubscribe request using signed URL parameters.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return response()->json([
                'message' => 'The unsubscribe link is invalid or has expired.',
            ], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $subscription = Subscription::where('email', $validated['email'])->first();

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
