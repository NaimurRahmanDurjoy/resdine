<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\HomeController;
use \App\Http\Controllers\WebController;
use Inertia\Inertia;
// use App\Http\Controllers\OrderController;

// Home page
Route::get('/', function () {
    return redirect()->route('web.menu');
})->name('home');

// Customer order pages
Route::get('/menu', [WebController::class, 'menu'])->name('web.menu');
Route::post('/order', [WebController::class, 'submitOrder'])->name('web.order.submit');

// Unauthorized page
Route::get('/unauthorized', function () {
    return view('errors.unauthorized');
})->name('unauthorized');
