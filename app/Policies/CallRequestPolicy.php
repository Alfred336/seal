<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\CallRequest;
use App\Models\User;

class CallRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::CallRequestsView->value);
    }

    public function view(User $user, CallRequest $callRequest): bool
    {
        return $user->can(Permission::CallRequestsView->value);
    }

    public function update(User $user, CallRequest $callRequest): bool
    {
        return $user->can(Permission::CallRequestsUpdate->value);
    }
}
