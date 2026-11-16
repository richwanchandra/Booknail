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
        });
    }
}
