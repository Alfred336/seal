<?php

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceController extends ApiController
{
    public function index(Request $request): ResourceCollection
    {
        abort_unless($request->user()->can(Permission::ServicesView->value), 403);

        $services = Service::query()->active()->ordered()->get();

        return ServiceResource::collection($services);
    }
}
