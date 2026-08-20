<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PlanInquiryRequest;
use App\Mail\NewPlanInquiry;
use App\Mail\PlanInquiryConfirmation;
use App\Models\PlanInquiry;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class PlanInquiryController extends ApiController
{
    public function store(PlanInquiryRequest $request): JsonResponse
    {
        $inquiry = PlanInquiry::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        // Client notification
        Mail::to($inquiry->email)
            ->send(new PlanInquiryConfirmation($inquiry));

        // SealTech notification
        $recipients = User::role(['admin', 'support'])->get();

        if ($recipients->isEmpty()) {
            $admin = User::where(
                'email',
                'admin@sealtech.test'
            )->first();

            if ($admin) {
                $recipients = collect([$admin]);
            }
        }

        if ($recipients->isNotEmpty()) {
            Mail::to($recipients)
                ->send(new NewPlanInquiry($inquiry));
        }

        return response()->json([
            'message' =>
                'Your plan inquiry has been received. Our team will contact you shortly.',

            'request_id' => $inquiry->id,

            'reference' =>
                'ST-PI-' .
                str_pad(
                    (string) $inquiry->id,
                    5,
                    '0',
                    STR_PAD_LEFT
                ),
        ], 201);
    }
}