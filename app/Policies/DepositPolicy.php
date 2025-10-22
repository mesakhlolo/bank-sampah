<?php

namespace App\Policies;

use App\Models\Deposit;
use App\Models\User;

class DepositPolicy
{
    /**
     * Determine if the user can view any deposits
     */
    public function viewAny(User $user): bool
    {
        // Both admin and operator can view deposits
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine if the user can view the deposit
     */
    public function view(User $user, Deposit $deposit): bool
    {
        // Both admin and operator can view deposits
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine if the user can create deposits
     */
    public function create(User $user): bool
    {
        // Both admin and operator can create deposits
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine if the user can update the deposit
     */
    public function update(User $user, Deposit $deposit): bool
    {
        // Only admin can update deposits (for now, no update feature yet)
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete the deposit
     */
    public function delete(User $user, Deposit $deposit): bool
    {
        // Only admin can delete deposits
        return $user->role === 'admin';
    }
}
