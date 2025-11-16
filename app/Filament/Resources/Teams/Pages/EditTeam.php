<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Support\Concerns\ValidatesTeamComposition;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    use ValidatesTeamComposition;

    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateTeamMembers($data['teamMembers'] ?? []);

        return $data;
    }
}
