<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ServicesView->value)
            || $user->can(Permission::ServicesManage->value);
    }

    public function view(User $user, Service $service): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::ServicesManage->value);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->can(Permission::ServicesManage->value);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->can(Permission::ServicesManage->value);
    }
}
