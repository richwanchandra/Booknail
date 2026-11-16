<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

trait HasAutoIncrementFallback
{
    /**
     * Boot the trait to ensure an auto-incrementing primary key value is always provided
     * even if the database table lacks AUTO_INCREMENT attribute in MySQL.
     */
    protected static function bootHasAutoIncrementFallback(): void
    {
        static::creating(function ($model) {
            $keyName = $model->getKeyName() ?? 'id';
            // Only assign if the primary key is currently empty or not set
            if (empty($model->getAttribute($keyName))) {
                $maxId = (int) DB::table($model->getTable())->max($keyName);
                $model->setAttribute($keyName, $maxId + 1);
            }

            // Pastikan kolom-kolom non-null dengan default di model Booking tidak pernah null
            if ($model instanceof \App\Models\Booking) {
                if ($model->getAttribute('delay_minutes') === null) {
                    $model->setAttribute('delay_minutes', 0);
                }
                if ($model->getAttribute('is_delayed') === null) {
                    $model->setAttribute('is_delayed', false);
                }
                if ($model->getAttribute('artist_confirmation_status') === null) {
                    $model->setAttribute('artist_confirmation_status', 'pending');
                }
                if ($model->getAttribute('booking_source') === null) {
                    $model->setAttribute('booking_source', 'WEB_ONLINE');
                }
                if ($model->getAttribute('branch') === null) {
                    $model->setAttribute('branch', 'Citra Garden 6');
                }
            }
        });
    }
}
