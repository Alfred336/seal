<?php

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectController extends ApiController
{
    public function index(Request $request): ResourceCollection
    {
        abort_unless($request->user()->can(Permission::ProjectsView->value), 403);

        $projects = Project::query()
            ->active()
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->ordered()
            ->get();

        return ProjectResource::collection($projects);
    }
}
