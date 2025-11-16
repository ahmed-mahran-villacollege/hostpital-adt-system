<?php

namespace App\Filament\Resources\Admissions\Pages;

use App\Filament\Resources\Admissions\AdmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditAdmission extends EditRecord
{
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
        $patientData = Arr::pull($data, 'patient');

        if ($patientData) {
            $this->record->patient()->update($patientData);
        }

        return $data;
    }
}
