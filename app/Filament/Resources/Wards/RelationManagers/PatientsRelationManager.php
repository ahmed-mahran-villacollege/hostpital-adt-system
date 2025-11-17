<?php

namespace App\Filament\Resources\Wards\RelationManagers;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PatientsRelationManager extends RelationManager
{
    protected static string $relationship = 'patients';

    protected static ?string $relatedResource = PatientResource::class;

    public function table(Table $table): Table
    {
        return $table;
    }
}
