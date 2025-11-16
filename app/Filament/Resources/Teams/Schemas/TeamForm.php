<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Models\Doctor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class TeamForm
{
    protected static array $doctorCache = [];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Select::make('consultant_id')
                    ->relationship('consultant', 'name')
                    ->label('Consultant'),
                Repeater::make('teamMembers')
                    ->label('Team members')
                    ->statePath('teamMembers')
                    ->minItems(1)
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Doctor')
                            ->options(fn (): array => self::getDoctorOptions())
                            ->searchable()
                            ->preload()
                            ->required()
                    ])
                    ->createItemButtonLabel('Add doctor'),
            ]);
    }

    protected static function getDoctorOptions(): array
    {
        return Doctor::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Doctor $doctor): array => [
                $doctor->id => sprintf('%s (Gr. %d)', $doctor->name, $doctor->grade),
            ])
            ->all();
    }
}
