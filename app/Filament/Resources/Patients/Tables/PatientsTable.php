<?php

namespace App\Filament\Resources\Patients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hospital_number')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sex')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Male' => 'primary',
                            'Female' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->searchable(),
                TextColumn::make('date_of_birth')
                    ->label('Age')
                    ->formatStateUsing(function ($state) {
                        return now()->format('Y') - $state->format('Y');
                    })
                    ->sortable(),
                TextColumn::make('admission.ward.name'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
