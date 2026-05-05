<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';

    protected $fillable = ['operator_user_id', 'action', 'metadata'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public static function record(string $action, array $metadata = []): void
    {
        static::create([
            'operator_user_id' => auth()->id() ? (string) auth()->id() : null,
            'action'           => $action,
            'metadata'         => $metadata ?: null,
        ]);
    }
}
