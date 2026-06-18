<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\Api\NewsletterRequest;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

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
}
