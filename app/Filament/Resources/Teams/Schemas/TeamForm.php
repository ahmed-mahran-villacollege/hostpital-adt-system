<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Models\Doctor;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

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
                    ->relationship()
                    ->label('Team members')
                    ->minItems(1)
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Doctor')
                            ->options(fn (): array => self::getDoctorOptions())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText(fn (Get $get): ?string => self::describeDoctor($get('doctor_id'))),
                    ])
                    ->createItemButtonLabel('Add doctor'),
            ]);
    }

    protected static function getDoctorOptions(): array
    {
        return Doctor::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    protected static function describeDoctor(mixed $doctorId): ?string
    {
        if (! $doctorId) {
            return null;
        }

        $doctor = self::$doctorCache[$doctorId] ??= Doctor::query()
            ->select(['id', 'rank', 'grade'])
            ->find($doctorId);

        if (! $doctor) {
            return null;
        }

        return "{$doctor->rank} · Grade {$doctor->grade}";
    }
}
