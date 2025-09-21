<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('web.index');
});
Route::get('/order', function () {return view('web.order');});
Route::get('/unauthorized', function() {
    return view('errors.unauthorized');
})->name('unauthorized');
