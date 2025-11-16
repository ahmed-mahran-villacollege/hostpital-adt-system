<?php

namespace App\Filament\Resources\Admissions\Pages;

use App\Filament\Resources\Admissions\AdmissionResource;
use App\Models\Patient;
use App\Support\Concerns\ValidatesWardAssignment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateAdmission extends CreateRecord
{
    use ValidatesWardAssignment;

    protected static string $resource = AdmissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $patientData = Arr::pull($data, 'patient');

        $this->validateWardAssignment(
            $data['ward_id'] ?? null,
            $patientData['sex'] ?? null,
        );

        $patient = Patient::query()->create($patientData);

        $data['patient_id'] = $patient->getKey();

        return $data;
    }
}
