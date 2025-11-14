<?php

namespace App\Filament\Resources\TreatedBy;

use App\Filament\Resources\TreatedBy\Pages\CreateTreatedBy;
use App\Filament\Resources\TreatedBy\Pages\EditTreatedBy;
use App\Filament\Resources\TreatedBy\Pages\ListTreatedBy;
use App\Filament\Resources\TreatedBy\Pages\ViewTreatedBy;
use App\Filament\Resources\TreatedBy\Schemas\TreatedByForm;
use App\Filament\Resources\TreatedBy\Schemas\TreatedByInfolist;
use App\Filament\Resources\TreatedBy\Tables\TreatedByTable;
use App\Models\TreatedBy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TreatedByResource extends Resource
{
    protected static ?string $model = TreatedBy::class;

    protected static ?string $pluralModelLabel = 'treated by';

    protected static ?string $slug = 'treated_by';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TreatedByForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TreatedByInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TreatedByTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreatedBy::route('/'),
            'create' => CreateTreatedBy::route('/create'),
            'view' => ViewTreatedBy::route('/{record}'),
            'edit' => EditTreatedBy::route('/{record}/edit'),
        ];
    }
}
