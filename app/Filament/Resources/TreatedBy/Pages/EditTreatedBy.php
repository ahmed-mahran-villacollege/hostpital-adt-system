<?php

namespace App\Filament\Resources\TreatedBy\Pages;

use App\Filament\Resources\TreatedBy\TreatedByResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTreatedBy extends EditRecord
{
    protected static string $resource = TreatedByResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
