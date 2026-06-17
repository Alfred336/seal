<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceController extends ApiController
{
    public function index(): ResourceCollection
    {
        $services = Service::query()->active()->ordered()->get();

        return ServiceResource::collection($services);
    }
}
