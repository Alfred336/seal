<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::PostsView->value)
            || $user->can(Permission::PostsManageAll->value);
    }

    public function view(User $user, Post $post): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        return $this->canManageAll($user) || $post->isOwnedBy($user);
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::PostsCreate->value)
            || $user->can(Permission::PostsManageAll->value);
    }

    public function update(User $user, Post $post): bool
    {
        if ($this->canManageAll($user)) {
            return true;
        }

        return $user->can(Permission::PostsUpdateOwn->value) && $post->isOwnedBy($user);
    }

    public function delete(User $user, Post $post): bool
    {
        if ($this->canManageAll($user)) {
            return true;
        }

        return $user->can(Permission::PostsDeleteOwn->value) && $post->isOwnedBy($user);
    }

    public function publish(User $user, Post $post): bool
    {
        if (! $user->can(Permission::PostsPublish->value)) {
            return false;
        }

        return $this->canManageAll($user) || $post->isOwnedBy($user);
    }

    private function canManageAll(User $user): bool
    {
        return $user->can(Permission::PostsManageAll->value);
    }
}
