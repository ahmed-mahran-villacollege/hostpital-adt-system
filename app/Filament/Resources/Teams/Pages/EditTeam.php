<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->action(function (Action $action) {
                    try {
                        $this->callHook('beforeDelete');
                        $this->getRecord()->delete();
                        $this->callHook('afterDelete');
                        $action->success();
                    } catch (QueryException $exception) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot delete team')
                            ->body('This team has linked patients and cannot be deleted.')
                            ->send();

                        $action->failure();
                    }
                }),
        ];
    }
}
