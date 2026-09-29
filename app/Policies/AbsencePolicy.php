<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;

class AbsencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('absences-view');
    }

    public function view(User $user, Absence $absence): bool
    {
        return $user->can('absences-view');
    }

    public function create(User $user): bool
    {
        return $user->can('absences-create');
    }

    public function update(User $user, Absence $absence): bool
    {
        return $user->can('absences-update')
            && ($user->isAdmin() || $absence->joueur?->user_id === $user->id);
    }

    public function delete(User $user, Absence $absence): bool
    {
        return $user->can('absences-delete')
            && ($user->isAdmin() || $absence->joueur?->user_id === $user->id);
    }
}
