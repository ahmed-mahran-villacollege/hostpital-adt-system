<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Support\Concerns\ValidatesTeamComposition;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    use ValidatesTeamComposition;

    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateTeamMembers($data['teamMembers'] ?? []);

        return $data;
    }
}
