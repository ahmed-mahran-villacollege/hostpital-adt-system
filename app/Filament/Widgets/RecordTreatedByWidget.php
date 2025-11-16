<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\RecordTreatedBy;
use Filament\Widgets\Widget;

class RecordTreatedByWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.record-treated-by-widget';

    public function getRecordTreatedByFormUrl(): string
    {
        return RecordTreatedBy::getUrl();
    }
}
