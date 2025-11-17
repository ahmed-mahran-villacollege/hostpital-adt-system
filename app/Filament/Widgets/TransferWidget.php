<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\TransferPatient;
use Filament\Widgets\Widget;

class TransferWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.transfer-widget';

    public static function canView(): bool
    {
        return auth()->user()?->can('patient.transfer') ?? false;
    }

    public function getTransferFormUrl(): string
    {
        return TransferPatient::getUrl();
    }
}
