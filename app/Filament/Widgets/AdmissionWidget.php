<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Admissions\AdmissionResource;
use Filament\Widgets\Widget;

class AdmissionWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.admission-widget';

    public static function canView(): bool
    {
        return auth()->user()?->can('patient.admit') ?? false;
    }

    public function getAdmissionFormUrl(): string
    {
        return AdmissionResource::getUrl('create');
    }
}
