<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function notImplemented(string $feature): JsonResponse
    {
        return response()->json([
            'message' => "{$feature} API will be available in a future release.",
        ], 501);
    }
}
