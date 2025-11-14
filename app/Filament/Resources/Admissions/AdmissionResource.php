<?php

namespace App\Filament\Resources\Admissions;

use App\Filament\Resources\Admissions\Pages\CreateAdmission;
use App\Filament\Resources\Admissions\Pages\EditAdmission;
use App\Filament\Resources\Admissions\Pages\ListAdmissions;
use App\Filament\Resources\Admissions\Pages\ViewAdmission;
use App\Filament\Resources\Admissions\Schemas\AdmissionForm;
use App\Filament\Resources\Admissions\Schemas\AdmissionInfolist;
use App\Filament\Resources\Admissions\Tables\AdmissionsTable;
use App\Models\Admission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdmissionResource extends Resource
{
    protected static ?string $model = Admission::class;

    protected static string|BackedEnum|null $navigationIcon = 'healthicons-o-insurance-card';

    public static function form(Schema $schema): Schema
    {
        return AdmissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdmissionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionsTable::configure($table);
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
            'index' => ListAdmissions::route('/'),
            'create' => CreateAdmission::route('/create'),
            'view' => ViewAdmission::route('/{record}'),
            'edit' => EditAdmission::route('/{record}/edit'),
        ];
    }
}
