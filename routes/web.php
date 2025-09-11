<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('web.index');
});
Route::get('/order', function () {return view('web.order');});
