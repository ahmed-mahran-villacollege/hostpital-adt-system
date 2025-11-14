<?php

namespace App\Filament\Resources\Admissions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AdmissionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('patient.name')
                    ->label('Patient'),
                TextEntry::make('ward.name')
                    ->label('Ward'),
                TextEntry::make('team.name')
                    ->label('Team'),
                TextEntry::make('admitted_at')
                    ->dateTime(),
                TextEntry::make('team.consultant.name')
                    ->label('Responsible Consultant'),
            ]);
    }
}
