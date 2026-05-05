<?php
namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    public function update(User $user, Block $block): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $user->tenant_id === $block->tenant_id
            && ($user->hasRole('admin') || $user->hasRole('editor'));
    }
}
