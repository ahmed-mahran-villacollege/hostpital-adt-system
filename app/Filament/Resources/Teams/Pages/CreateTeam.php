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

    /**
     * Validate team members before create.
     */
    protected function beforeValidate(): void
    {
        $state = $this->form->getState();

        $this->teamMembersInput = $state['teamMembers'] ?? [];

        $this->validateTeamMembers($this->teamMembersInput);
    }

    /**
     * Remove team member repeater data before saving the team.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['teamMembers']);

        return $data;
    }

    /**
     * Save team members after creating the team record.
     */
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
