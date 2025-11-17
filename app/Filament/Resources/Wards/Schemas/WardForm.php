<?php

namespace App\Filament\Resources\Wards\Schemas;

use Closure;
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
                    ->minValue(4)
                    ->rule(function (TextInput $component): Closure {
                        return function (string $attribute, $value, Closure $fail) use ($component): void {
                            $record = $component->getRecord();

                            if (! $record) {
                                return;
                            }

                            $occupied = $record->occupiedBeds();
                            $newCapacity = (int) $value;

                            if ($newCapacity < $occupied) {
                                $fail("Capacity cannot be lower than current occupancy ({$occupied}).");
                            }
                        };
                    }),
            ]);
    }
}
