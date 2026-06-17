<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\ProjectRequest;
use App\Models\User;

class ProjectRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ProjectRequestsView->value);
    }

    public function view(User $user, ProjectRequest $projectRequest): bool
    {
        return $user->can(Permission::ProjectRequestsView->value);
    }

    public function update(User $user, ProjectRequest $projectRequest): bool
    {
        return $user->can(Permission::ProjectRequestsUpdate->value);
    }
}
