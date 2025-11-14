<?php

namespace App\Filament\Resources\Admissions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class AdmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->required(),
                Select::make('ward_id')
                    ->relationship('ward', 'name')
                    ->required(),
                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->required(),
                DateTimePicker::make('admitted_at')
                    ->required(),
            ]);
    }
}
