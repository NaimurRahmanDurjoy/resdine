<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-api', fn() => response()->json(['status' => 'API working']));
