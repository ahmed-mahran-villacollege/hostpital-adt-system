<?php

namespace App\Filament\Resources\Admissions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class AdmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Patient')
                    ->columns(2)
                    ->schema([
                        TextInput::make('patient.hospital_number')
                            ->label('Hospital number')
                            ->required(),
                        TextInput::make('patient.name')
                            ->required(),
                        DatePicker::make('patient.date_of_birth')
                            ->required(),
                        Select::make('patient.sex')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ])
                            ->required(),
                    ]),
                Section::make('Admission Details')
                    ->columns(2)
                    ->schema([
                        Select::make('ward_id')
                            ->relationship('ward', 'name')
                            ->required(),
                        Select::make('team_id')
                            ->relationship('team', 'name')
                            ->required(),
                        DateTimePicker::make('admitted_at')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
