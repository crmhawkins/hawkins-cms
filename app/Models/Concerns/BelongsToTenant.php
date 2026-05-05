<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            if (function_exists('tenant') && tenant()) {
                $query->where('tenant_id', tenant('id'));
            }
        });

        static::creating(function ($model) {
            if (function_exists('tenant') && tenant() && empty($model->tenant_id)) {
                $model->tenant_id = tenant('id');
            }
        });
    }

    public function initializeBelongsToTenant(): void
    {
        $this->fillable[] = 'tenant_id';
    }
}
