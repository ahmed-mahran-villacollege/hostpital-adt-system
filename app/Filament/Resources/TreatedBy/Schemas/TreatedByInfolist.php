<?php

namespace App\Filament\Resources\TreatedBy\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TreatedByInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('admission_id')
                    ->numeric(),
                TextEntry::make('doctor_id')
                    ->numeric(),
                TextEntry::make('treated_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
