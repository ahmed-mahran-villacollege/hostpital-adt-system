<?php

namespace App\Filament\Resources\Admissions\Pages;

use App\Filament\Resources\Admissions\AdmissionResource;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateAdmission extends CreateRecord
{
    protected static string $resource = AdmissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $patientData = Arr::pull($data, 'patient');

        $patient = Patient::query()->create($patientData ?? []);

        $data['patient_id'] = $patient->getKey();

        return $data;
    }
}
