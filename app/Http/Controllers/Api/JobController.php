<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\JobResource;
use App\Models\OpenPosition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $jobs = OpenPosition::query()->published()->latest()->get();

        return JobResource::collection($jobs);
    }

    public function show(string $slug, Request $request): JobResource
    {
        $job = OpenPosition::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new JobResource($job);
    }
}
