<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\HomeController;
use Inertia\Inertia;
// use App\Http\Controllers\OrderController;

// Route::get('/', function () {
//     return view('web.index');
// });
// Route::get('/order', function () {return view('web.order');});
Route::get('/unauthorized', function() {
    return view('errors.unauthorized');
})->name('unauthorized');



use \App\Http\Controllers\WebController;

// Home page
Route::get('/', function() {
    return redirect()->route('web.menu');
})->name('home');

// Customer order pages
Route::get('/menu', [WebController::class, 'menu'])->name('web.menu');
Route::post('/order', [WebController::class, 'submitOrder'])->name('web.order.submit');

Route::get('/test-middleware', function () {
    return "Middleware loaded!";
})->middleware('role:admin');

Route::get('/inertia-test', function () {
    return Inertia::render('Test');
});