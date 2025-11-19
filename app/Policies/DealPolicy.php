<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DealPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-deals') || $user->can('view-all-deals');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Deal $deal): bool
    {
        return $user->can('view-all-deals')
            || ($user->can('view-deals') && $deal->assigned_to_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-deals');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Deal $deal): bool
    {
        return $user->can('edit-all-deals')
            || ($user->can('edit-deals') && $deal->assigned_to_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Deal $deal): bool
    {
        return $user->can('delete-all-deals')
            || ($user->can('delete-deals') && $deal->assigned_to_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Deal $deal): bool
    {
        return $user->can('delete-all-deals');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Deal $deal): bool
    {
        return $user->can('delete-all-deals');
    }

    /**
     * Determine whether the user can manage deal stages.
     */
    public function manageStages(User $user): bool
    {
        return $user->can('manage-stages-deals');
    }
}
