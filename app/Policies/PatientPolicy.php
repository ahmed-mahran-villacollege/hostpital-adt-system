<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canAny([
            'view.ward_patient_list',
            'view.team_patient_list',
            'view.patient_treatment_list',
            'patient.admit',
            'patient.transfer',
            'patient.discharge',
            'patient.record_treatment',
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient $patient): bool
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
    public function update(User $user, Patient $patient): bool
    {
        return $user->canAny([
            'patient.transfer',
            'patient.record_treatment',
            'patient.discharge',
        ]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->can('patient.discharge');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Patient $patient): bool
    {
        return $user->can('patient.discharge');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Patient $patient): bool
    {
        return $user->can('patient.discharge');
    }
}
