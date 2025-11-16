<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Support\Concerns\ValidatesTeamComposition;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    use ValidatesTeamComposition;

    protected static string $resource = TeamResource::class;

    protected array $teamMembersInput = [];

    protected function beforeValidate(): void
    {
        $state = $this->form->getState();

        $this->teamMembersInput = $state['teamMembers'] ?? [];

        $this->validateTeamMembers($this->teamMembersInput);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['teamMembers']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $members = collect($this->teamMembersInput)
            ->filter(fn ($item) => filled($item['doctor_id'] ?? null))
            ->map(fn ($item): array => ['doctor_id' => $item['doctor_id']])
            ->values()
            ->all();

        if (! empty($members)) {
            $this->record->teamMembers()->createMany($members);
        }

        $this->teamMembersInput = [];
    }
}
