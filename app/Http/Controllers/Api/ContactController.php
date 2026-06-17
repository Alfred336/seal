<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ContactRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\JsonResponse;

class ContactController extends ApiController
{
    public function store(ContactRequest $request): JsonResponse
    {
        ContactSubmission::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['message' => 'Message received. We will be in touch soon.'], 201);
    }
}
