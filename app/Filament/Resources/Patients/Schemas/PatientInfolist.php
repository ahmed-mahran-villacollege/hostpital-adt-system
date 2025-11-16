<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        TextEntry::make('hospital_number'),
                        TextEntry::make('name'),
                        TextEntry::make('date_of_birth')
                            ->label('Age')
                            ->formatStateUsing(function ($state) {
                                return now()->format('Y') - $state->format('Y').' years (DOB: '.$state->format('M d, Y').')';
                            }),
                        TextEntry::make('sex')
                            ->badge()
                            ->color(function ($state) {
                                return match ($state) {
                                    'Male' => 'primary',
                                    'Female' => 'danger',
                                    default => 'gray',
                                };
                            }),
                    ])->columns(2)->columnSpan(2),
                Section::make('')
                    ->schema([
                        TextEntry::make('admission.team.name')
                            ->label('Assigned Team')
                            ->formatStateUsing(function ($state, $record) {
                                return $state.' ('.$record->admission->team->code.')';
                            }),
                        TextEntry::make('admission.team.consultant.name')
                            ->label('Responsible Consultant'),
                    ])->columns(2),
                Section::make('')
                    ->schema([
                        TextEntry::make('admission.ward.name')
                            ->label('Ward'),
                        TextEntry::make('admission.ward.type')
                            ->label('Type')
                            ->badge()
                            ->color(function ($state) {
                                return match ($state) {
                                    'Male' => 'primary',
                                    'Female' => 'danger',
                                    default => 'gray',
                                };
                            }),
                    ])->columns(2),
            ]);
    }
}
