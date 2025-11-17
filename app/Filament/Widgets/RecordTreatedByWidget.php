<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\RecordTreatedBy;
use Filament\Widgets\Widget;

class RecordTreatedByWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.record-treated-by-widget';

    public static function canView(): bool
    {
        return auth()->user()?->can('patient.record_treatment') ?? false;
    }

    public function getRecordTreatedByFormUrl(): string
    {
        return RecordTreatedBy::getUrl();
    }
}
