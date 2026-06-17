<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::SubscriptionsView->value);
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $user->can(Permission::SubscriptionsView->value);
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $user->can(Permission::SubscriptionsManage->value);
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->can(Permission::SubscriptionsManage->value);
    }
}
