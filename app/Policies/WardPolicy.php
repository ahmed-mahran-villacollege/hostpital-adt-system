<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ward;
use Illuminate\Auth\Access\Response;

class WardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canAny([
            'view.ward_patient_list',
            'ward.create',
            'ward.update',
            'ward.delete',
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ward $ward): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ward.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ward $ward): bool
    {
        return $user->can('ward.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ward $ward): bool
    {
        return $user->can('ward.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ward $ward): bool
    {
        return $user->can('ward.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ward $ward): bool
    {
        return $user->can('ward.delete');
    }
}
