<?php
namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    public function update(User $user, Block $block): bool
    {
        return $user->hasRole(['superadmin', 'admin', 'editor']);
    }
}
