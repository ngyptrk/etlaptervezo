<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    /**
     * Admin mindent csinálhat
     */
    public function before(User $user, string $ability): bool|null
    {
        // ADMIN ROLE (ha nem 1, írd át)
        if ((int) $user->role === 1) {
            return true;
        }

        return false; // nem admin → minden tiltva
    }

    /**
     * Az alábbi metódusok technikailag már nem is futnak adminnál,
     * de jó itt hagyni őket olvashatóság miatt.
     */

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Unit $unit): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Unit $unit): bool
    {
        return false;
    }

    public function delete(User $user, Unit $unit): bool
    {
        return false;
    }

    public function restore(User $user, Unit $unit): bool
    {
        return false;
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return false;
    }
}
