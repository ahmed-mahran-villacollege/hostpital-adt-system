<?php

namespace App\Filament\Resources\TreatedBy\Pages;

use App\Filament\Resources\TreatedBy\TreatedByResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTreatedBy extends ViewRecord
{
    protected static string $resource = TreatedByResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
