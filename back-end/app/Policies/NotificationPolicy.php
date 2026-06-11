<?php

namespace App\Policies;

use App\Models\User;

class NotificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, \Illuminate\Notifications\DatabaseNotification $notification): bool
    {
        return $user->can('manage notifications')
            || ($user->hasRole('student') && $notification->user_id == $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, \Illuminate\Notifications\DatabaseNotification $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, \Illuminate\Notifications\DatabaseNotification $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, \Illuminate\Notifications\DatabaseNotification $notification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, \Illuminate\Notifications\DatabaseNotification $notification): bool
    {
        return false;
    }
}
