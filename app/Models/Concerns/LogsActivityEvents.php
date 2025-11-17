<?php

namespace App\Models\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Shared activity logging defaults for all models.
 */
trait LogsActivityEvents
{
    use LogsActivity;

    /**
     * Log all attributes for create/update/delete/restore events.
     */
    protected static array $recordEvents = ['created', 'updated', 'deleted', 'restored'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
