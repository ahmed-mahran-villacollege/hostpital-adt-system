<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Admissions\AdmissionResource;
use Filament\Widgets\Widget;

class TransferWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.transfer-widget';

    public function getTransferFormUrl(): string
    {
        return AdmissionResource::getUrl();
    }
}
