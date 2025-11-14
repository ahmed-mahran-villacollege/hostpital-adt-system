<?php

namespace App\Filament\Resources\TreatedBy\Pages;

use App\Filament\Resources\TreatedBy\TreatedByResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTreatedBy extends ListRecords
{
    protected static string $resource = TreatedByResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
