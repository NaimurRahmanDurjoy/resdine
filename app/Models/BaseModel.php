<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = static::getActedUser();
            if ($user && ! $model->created_by) {
                $model->created_by = $user->id;
                // $model->created_by_type = static::getUserType($user);
            }
        });

        static::updating(function ($model) {
            $user = static::getActedUser();
            if ($user) {
                $model->updated_by = $user->id;
                // $model->updated_by_type = static::getUserType($user);
            }
        });

        static::deleting(function ($model) {
            if (! $model->isForceDeleting()) {
                $user = static::getActedUser();
                if ($user) {
                    $model->deleted_by = $user->id;
                    // $model->deleted_by_type = static::getUserType($user);
                    $model->saveQuietly();
                }
            }
        });
    }

    protected static function getActedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }
        
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }

        if (Auth::guard('api')->check()) {
            return Auth::guard('api')->user();
        }

        return null;
    }

    protected static function getUserType($user)
    {
        if ($user instanceof \App\Models\Admin) {
            return 1; // Admin
        }

        if ($user instanceof \App\Models\User) {
            return 2; // Regular user
        }

        return null;
    }
}
