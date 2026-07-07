<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CallRequest;
use App\Mail\CallRequestConfirmation;
use App\Mail\NewCallRequest;
use App\Models\CallRequest as CallRequestModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class CallController extends ApiController
{
    public function store(CallRequest $request): JsonResponse
    {
        $requestModel = CallRequestModel::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        // Send confirmation to the visitor who submitted the form
        Mail::to($requestModel->email)->send(new CallRequestConfirmation($requestModel));

        // Notify the admin/support team
        $recipients = User::role(['admin', 'support'])->get();
        if ($recipients->isEmpty()) {
            $admin = User::where('email', 'admin@sealtech.test')->first();
            if ($admin) {
                $recipients = collect([$admin]);
            }
        }

        if ($recipients->isNotEmpty()) {
            Mail::to($recipients)->send(new NewCallRequest($requestModel));
        }

        return response()->json(['message' => 'Call request received. We will confirm your booking shortly.'], 201);
    }
}
