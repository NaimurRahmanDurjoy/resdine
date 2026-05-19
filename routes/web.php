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
Route::any('/payment/callback/{gateway}', [App\Http\Controllers\PaymentCallbackController::class, 'handleCallback'])->name('payment.callback');
Route::get('/payment/simulator/{gateway}', [App\Http\Controllers\PaymentSimulatorController::class, 'showSimulator'])->name('payment.simulator');

// Unauthorized page
Route::get('/unauthorized', function () {
    return view('errors.unauthorized');
})->name('unauthorized');
