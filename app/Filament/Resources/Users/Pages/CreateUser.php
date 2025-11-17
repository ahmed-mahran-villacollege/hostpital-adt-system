<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @var array<int, string>
     */
    protected array $rolesToSync = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->rolesToSync = $data['roles'] ?? [];
        unset($data['roles']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (method_exists($this->record, 'syncRoles')) {
            $this->record->syncRoles($this->rolesToSync);
        }
    }
}
