<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ProjectRequestRequest;
use App\Models\ProjectRequest;
use Illuminate\Http\JsonResponse;

class ProjectRequestController extends ApiController
{
    public function store(ProjectRequestRequest $request): JsonResponse
    {
        ProjectRequest::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['message' => 'Project request received. Our team will review it and get back to you.'], 201);
    }
}
