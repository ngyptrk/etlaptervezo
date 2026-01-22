<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    /**
     * Admin (role_id = 1) mindent tud.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ((int) $user->role_id === 1) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return (int) $user->role_id === 2;
    }

    public function view(User $user, Unit $unit): bool
    {
        return (int) $user->role_id === 2;
    }

    public function create(User $user): bool
    {
        return (int) $user->role_id === 2;
    }

    public function update(User $user, Unit $unit): bool
    {
        return (int) $user->role_id === 2 && (int) $unit->user_id === (int) $user->id;
    }

    public function delete(User $user, Unit $unit): bool
    {
        return (int) $user->role_id === 2 && (int) $unit->user_id === (int) $user->id;
    }
}
