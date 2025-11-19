<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-leads') || $user->can('view-all-leads');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lead $lead): bool
    {
        // User can view if they have view-all-leads permission
        // Or if they own the lead and have view-leads permission
        return $user->can('view-all-leads')
            || ($user->can('view-leads') && $lead->assigned_to_id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-leads');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lead $lead): bool
    {
        // User can update if they have edit-all-leads permission
        // Or if they own the lead and have edit-leads permission
        return $user->can('edit-all-leads')
            || ($user->can('edit-leads') && $lead->assigned_to_id === $user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lead $lead): bool
    {
        // User can delete if they have delete-all-leads permission
        // Or if they own the lead and have delete-leads permission
        return $user->can('delete-all-leads')
            || ($user->can('delete-leads') && $lead->assigned_to_id === $user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('delete-all-leads');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('delete-all-leads');
    }

    /**
     * Determine whether the user can assign the lead to another user.
     */
    public function assign(User $user): bool
    {
        return $user->can('assign-leads');
    }

    /**
     * Determine whether the user can convert the lead to a contact.
     */
    public function convert(User $user, Lead $lead): bool
    {
        return $user->can('convert-leads')
            && ($user->can('edit-all-leads') || $lead->assigned_to_id === $user->id);
    }
}
