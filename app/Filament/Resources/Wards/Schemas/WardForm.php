<?php

namespace App\Filament\Resources\Wards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                    ])
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->minValue(4),
            ]);
    }
}
