<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canAny([
            'patient.admit',
            'patient.transfer',
            'patient.discharge',
            'view.ward_patient_list',
            'view.team_patient_list',
            'view.patient_treatment_list',
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Admission $admission): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('patient.admit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Admission $admission): bool
    {
        return $user->can('patient.transfer');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Admission $admission): bool
    {
        return $user->can('patient.discharge');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Admission $admission): bool
    {
        return $user->can('patient.discharge');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Admission $admission): bool
    {
        return $user->can('patient.discharge');
    }
}
