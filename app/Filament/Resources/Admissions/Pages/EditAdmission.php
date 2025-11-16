<?php

namespace App\Filament\Resources\Admissions\Pages;

use App\Filament\Resources\Admissions\AdmissionResource;
use App\Support\Concerns\ValidatesWardAssignment;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditAdmission extends EditRecord
{
    use ValidatesWardAssignment;

    protected static string $resource = AdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->label('Discharge'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $patient = $this->record->patient;

        $data['patient'] = $patient
            ? Arr::only($patient->toArray(), [
                'hospital_number',
                'name',
                'date_of_birth',
                'sex',
            ])
            : [];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $patientData = Arr::pull($data, 'patient') ?? [];
        $patientSex = $patientData['sex'] ?? $this->record->patient?->sex;
        $wardId = (int) ($data['ward_id'] ?? $this->record->ward_id);

        $originalWardId = (int) $this->record->getOriginal('ward_id');

        $this->validateWardAssignment(
            $wardId,
            $patientSex,
            'ward_id',
            $originalWardId === $wardId,
        );

        if (! empty($patientData)) {
            $this->record->patient()->update($patientData);
        }

        return $data;
    }
}
