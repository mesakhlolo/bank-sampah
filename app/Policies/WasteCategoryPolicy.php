<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WasteCategory;

class WasteCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admin and operator can view
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WasteCategory $wasteCategory): bool
    {
        return true; // Both admin and operator can view
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WasteCategory $wasteCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WasteCategory $wasteCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WasteCategory $wasteCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WasteCategory $wasteCategory): bool
    {
        return $user->isAdmin();
    }
}
