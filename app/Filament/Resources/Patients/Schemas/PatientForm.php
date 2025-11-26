<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Models\Patient;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hospital_number')
                    ->required()
                    ->rule(function (TextInput $component): Closure {
                        return function (string $attribute, $value, Closure $fail) use ($component): void {
                            if (blank($value)) {
                                return;
                            }

                            $query = Patient::query()
                                ->where('hospital_number', $value);

                            if ($patientId = $component->getRecord()?->getKey()) {
                                $query->whereKeyNot($patientId);
                            }

                            if ($query->exists()) {
                                $fail('This hospital number already exists.');
                            }
                        };
                    }),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('date_of_birth')
                    ->required(),
                Select::make('sex')
                    ->disabled(fn ($record) => $record)
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->required(),
            ]);
    }
}
