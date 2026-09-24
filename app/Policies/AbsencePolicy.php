<?php

namespace App\Policies;

use App\Models\Absence;
use App\Models\User;

class AbsencePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Absence $absence): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->joueur()->exists() || $user->isAdmin();
    }

    public function update(User $user, Absence $absence): bool
    {
        return $this->canManage($user, $absence);
    }

    public function delete(User $user, Absence $absence): bool
    {
        return $this->canManage($user, $absence);
    }

    private function canManage(User $user, Absence $absence): bool
    {
        return $user->isAdmin() || $absence->joueur?->user_id === $user->id;
    }
}
