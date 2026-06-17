<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CallRequest;
use App\Models\CallRequest as CallRequestModel;
use Illuminate\Http\JsonResponse;

class CallController extends ApiController
{
    public function store(CallRequest $request): JsonResponse
    {
        CallRequestModel::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['message' => 'Call request received. We will confirm your booking shortly.'], 201);
    }
}
