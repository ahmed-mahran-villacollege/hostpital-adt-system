<?php

namespace App\Filament\Resources\Admissions\Schemas;

use App\Models\Patient;
use App\Support\WardCapacityFormatter;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                            ->required()
                            ->rule(function (TextInput $component): Closure {
                                return function (string $attribute, $value, Closure $fail) use ($component): void {
                                    if (blank($value)) {
                                        return;
                                    }

                                    $query = Patient::query()
                                        ->where('hospital_number', $value);

                                    if ($patientId = $component->getRecord()?->patient?->getKey()) {
                                        $query->whereKeyNot($patientId);
                                    }

                                    if ($query->exists()) {
                                        $fail('This hospital number already exists.');
                                    }
                                };
                            }),
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
                            ->live()
                            ->required(),
                        Select::make('team_id')
                            ->relationship('team', 'name')
                            ->required(),
                        DateTimePicker::make('admitted_at')
                            ->required()
                            ->default(now())
                            ->columnSpanFull(),
                        Placeholder::make('ward_occupancy')
                            ->label('Ward occupancy:')
                            ->content(fn (callable $get) => WardCapacityFormatter::forWardId($get('ward_id')))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
