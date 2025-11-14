<?php

namespace App\Filament\Resources\Admissions\RelationManagers;

use App\Filament\Resources\TreatedBy\TreatedByResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TreatedByRelationManager extends RelationManager
{
    protected static string $relationship = 'treatedBy';

    protected static ?string $relatedResource = TreatedByResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
