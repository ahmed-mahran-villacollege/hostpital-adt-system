<?php

namespace App\Filament\Resources\Wards\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WardInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('type')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Male' => 'primary',
                            'Female' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextEntry::make('capacity')
                    ->numeric(),
                TextEntry::make('occupancy')
                    ->label('Occupancy')
                    ->state(fn ($record) => sprintf(
                        '%d / %d beds occupied (Free: %d)',
                        $record->occupiedBeds(),
                        $record->capacity ?? 0,
                        $record->freeBeds(),
                    )),
            ]);
    }
}
