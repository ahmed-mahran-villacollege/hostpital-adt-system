<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @var array<int, string>
     */
    protected array $rolesToSync = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->rolesToSync = $data['roles'] ?? [];
        unset($data['roles']);

        return $data;
    }

    protected function afterSave(): void
    {
        if (method_exists($this->record, 'syncRoles')) {
            $this->record->syncRoles($this->rolesToSync);
        }
    }
}
