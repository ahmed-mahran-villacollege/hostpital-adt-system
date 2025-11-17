<?php

namespace App\Filament\Resources\Admissions\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TreatedByRelationManager extends RelationManager
{
    protected static string $relationship = 'treatedBy';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->required(),
                DateTimePicker::make('treated_at')
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('doctor.name')
                    ->label('Doctor')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->doctor->rank === 'Consultant' ? $state : $state.' (Gr. '.$record->doctor->grade.')';
                    }),
                TextEntry::make('treated_at')
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('doctor.name')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->doctor->rank === 'Consultant' ? $state : $state.' (Gr. '.$record->doctor->grade.')';
                    })
                    ->searchable(),
                TextColumn::make('treated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('treated_at')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
