<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\DischargePatient;
use Filament\Widgets\Widget;

class DischargeWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.discharge-widget';

    public function getDischargeFormUrl(): string
    {
        return DischargePatient::getUrl();
    }
}
