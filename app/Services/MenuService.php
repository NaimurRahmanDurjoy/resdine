<?php

namespace App\Services;

use App\Models\User;
use App\Models\SoftwareMenu;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    public function getForUser(User $user)
    {
        return Cache::remember("user_menus_{$user->id}", 3600, function () use ($user) {
            return SoftwareMenu::whereHas('users', fn($q) => $q->where('user_id', $user->id))
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with('childrenRecursive')
                ->get();
        });
    }

    public function clearCacheForUser(User $user)
    {
        Cache::forget("user_menus_{$user->id}");
    }
}
