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

    protected array $teamMembersInput = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function beforeValidate(): void
    {
        $state = $this->form->getState();

        $this->teamMembersInput = $state['teamMembers'] ?? [];

        $this->validateTeamMembers($this->teamMembersInput);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['teamMembers'] = $this->record->teamMembers()
            ->get()
            ->map(fn ($member): array => ['doctor_id' => $member->doctor_id])
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['teamMembers']);

        return $data;
    }

    protected function afterSave(): void
    {
        $members = collect($this->teamMembersInput)
            ->filter(fn ($item) => filled($item['doctor_id'] ?? null))
            ->map(fn ($item): array => ['doctor_id' => $item['doctor_id']])
            ->values()
            ->all();

        $this->record->teamMembers()->delete();

        if (! empty($members)) {
            $this->record->teamMembers()->createMany($members);
        }

        $this->teamMembersInput = [];
    }
}
