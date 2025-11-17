<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'hospital_admin' => [
                'ward.create',
                'ward.update',
                'ward.delete',
                'team.create',
                'team.update',
                'team.delete',
                'team.assign_doctor',
                'patient.admit',
                'patient.discharge',
                'patient.transfer',
                'patient.record_treatment',
                'view.ward_patient_list',
                'view.team_patient_list',
                'view.patient_treatment_list',
            ],
            'system_admin' => [
                'user.create',
                'user.update',
                'user.delete',
                'audit.view',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            $role->syncPermissions($permissions);
        }
    }
}
