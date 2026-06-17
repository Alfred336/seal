<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\ContactSubmission;
use App\Models\User;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::ContactSubmissionsView->value);
    }

    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can(Permission::ContactSubmissionsView->value);
    }

    public function update(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can(Permission::ContactSubmissionsUpdate->value);
    }
}
