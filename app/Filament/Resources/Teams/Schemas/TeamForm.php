<?php

namespace App\Filament\Resources\Teams\Schemas;

use App\Filament\Resources\Teams\Pages\CreateTeam;
use App\Models\Doctor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->unique()
                    ->required(),
                Select::make('consultant_id')
                    ->label('Consultant')
                    ->relationship(
                        name: 'consultant',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->where('rank', 'Consultant'),
                    ),
                Repeater::make('teamMembers')
                    ->label('Team members')
                    ->statePath('teamMembers')
                    ->minItems(1)
                    ->visible(fn ($livewire) => $livewire instanceof CreateTeam)
                    ->schema([
                        Select::make('doctor_id')
                            ->label('Doctor')
                            ->options(fn (): array => self::getDoctorOptions())
                            ->searchable()
                            ->preload()
                            ->required(),
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
