<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockRevision extends Model
{
    protected $fillable = ['block_id', 'content', 'user_id'];

    protected $casts = ['content' => 'array'];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
