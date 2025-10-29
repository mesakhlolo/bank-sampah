<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Withdrawal;

class WithdrawalPolicy
{
    /**
     * Determine if the user can view any withdrawals
     */
    public function viewAny(User $user): bool
    {
        // Both admin and operator can view withdrawals
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine if the user can view the withdrawal
     */
    public function view(User $user, Withdrawal $withdrawal): bool
    {
        // Both admin and operator can view withdrawals
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine if the user can create withdrawals
     */
    public function create(User $user): bool
    {
        // Both admin and operator can create withdrawals
        return in_array($user->role, ['admin', 'operator']);
    }

    /**
     * Determine if the user can update the withdrawal
     */
    public function update(User $user, Withdrawal $withdrawal): bool
    {
        // Only admin can update withdrawals (for now, no update feature yet)
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete the withdrawal
     */
    public function delete(User $user, Withdrawal $withdrawal): bool
    {
        // Only admin can delete withdrawals
        return $user->role === 'admin';
    }
}
