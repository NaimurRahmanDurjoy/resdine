<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

// Base URL Generator
if (!function_exists('base_url')) {
    function base_url(): string
    {
        $baseRoute = Str::beforeLast(Route::currentRouteName(), '.');
        $path = str_replace('.', '/', $baseRoute);
        return url($path);
    }
}
// // Livewire Admin Component Resolver
// if (! function_exists('livewire')) {
//     function livewire(string $component)
//     {
//         $class = 'App\\Http\\Livewire\\' . str_replace('/', '\\', $component);

//         return $class::class;  // <-- THIS FIXES EVERYTHING
//     }
// }

