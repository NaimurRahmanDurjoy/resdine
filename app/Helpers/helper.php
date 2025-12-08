<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if (!function_exists('base_url')) {
    function base_url(): string
    {
        $baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
        $path = str_replace('.', '/', $baseRoute);
        return url($path);
    }
}
