<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
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
                        TextEntry::make('event')
                            ->badge()
                            ->color(fn ($state): string => self::eventColor($state)),
                        TextEntry::make('subject')
                            ->label('Subject')
                            ->state(fn (Activity $record): string => self::formatSubject($record)),
                        TextEntry::make('causer.name')
                            ->label('Causer')
                            ->placeholder('System'),
                        TextEntry::make('created_at')
                            ->label('When')
                            ->dateTime(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->visible(fn($record) => $record->description !== $record->event)
                            ->columnSpanFull(),
                    ])->columns(4)->columnSpanFull(),
                Section::make('Properties')
                    ->schema([
                        KeyValueEntry::make('properties.attributes')
                            ->label('Attributes')
                            ->placeholder('No attributes recorded.'),
                        KeyValueEntry::make('properties.old')
                            ->label('Old attributes')
                            ->placeholder('No previous values recorded.'),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('Subject')
                    ->state(fn (Activity $record): string => self::formatSubject($record))
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query->where(function (Builder $query) use ($search): void {
                                $query->where('subject_type', 'like', "%{$search}%")
                                    ->orWhere('subject_id', 'like', "%{$search}%")
                                    ->orWhere('description', 'like', "%{$search}%")
                                    ->orWhere('event', 'like', "%{$search}%");
                            });
                        },
                    ),
                TextColumn::make('event')
                    ->badge()
                    ->sortable()
                    ->color(fn ($state): string => self::eventColor($state))
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable()
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

    protected static function formatSubject(Activity $activity): string
    {
        $type = class_basename($activity->subject_type ?? '') ?: 'Unknown';
        $id = $activity->subject_id ?? '';

        return trim("{$type} #{$id}");
    }

    protected static function eventColor(?string $event): string
    {
        return match (Str::lower($event ?? '')) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            default => 'gray',
        };
    }
}
