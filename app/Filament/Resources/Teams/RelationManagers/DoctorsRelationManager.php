<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DoctorsRelationManager extends RelationManager
{
    protected static string $relationship = 'doctors';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('rank')
                    ->required()
                    ->default('Junior'),
                TextInput::make('grade')
                    ->required()
                    ->default('1'),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->rank === 'Consultant' ? $state : $state.' (Gr. '.$record->grade.')';
                    }),
                TextEntry::make('rank'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->formatStateUsing(function (string $state, $record) {
                        return $state.' (Gr. '.$record->grade.')';
                    })
                    ->searchable(),
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
            ->headerActions([
                AttachAction::make()
                    ->label('Add')
                    ->modalHeading('Add Doctor')
                    ->modalSubmitActionLabel('Add')
                    ->disableAttachAnother()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                ViewAction::make(),
                DetachAction::make()
                    ->label('Remove'),
            ]);
    }
}
