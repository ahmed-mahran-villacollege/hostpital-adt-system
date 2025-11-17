<?php

namespace App\Filament\Resources\Wards\Pages;

use App\Filament\Resources\Wards\WardResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;

class EditWard extends EditRecord
{
    protected static string $resource = WardResource::class;

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
                            ->title('Cannot delete ward')
                            ->body('This ward has admitted patients and cannot be deleted.')
                            ->send();

                        $action->failure();
                    }
                }),
        ];
    }
}
