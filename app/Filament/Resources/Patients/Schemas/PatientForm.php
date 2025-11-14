<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hospital_number')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('date_of_birth')
                    ->required(),
                TextInput::make('sex')
                    ->required(),
            ]);
    }
}
