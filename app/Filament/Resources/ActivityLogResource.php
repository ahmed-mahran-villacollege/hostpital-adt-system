<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 200;

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('event')
                            ->badge(),
                        TextEntry::make('subject_type')
                            ->label('Subject type')
                            ->formatStateUsing(fn (?string $state): string => class_basename($state ?? '')),
                        TextEntry::make('subject_id')
                            ->label('Subject ID'),
                        TextEntry::make('causer.name')
                            ->label('Causer')
                            ->placeholder('System'),
                        TextEntry::make('created_at')
                            ->label('When')
                            ->dateTime(),
                    ])->columns(2),
                Section::make('Properties')
                    ->schema([
                        TextEntry::make('properties')
                            ->formatStateUsing(fn ($state): string => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->copyable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('event')
                    ->badge()
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subject type')
                    ->formatStateUsing(fn (?string $state): string => class_basename($state ?? ''))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject_id')
                    ->label('Subject ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('causer.name')
                    ->label('Causer')
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
