<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\HomeController;
// use App\Http\Controllers\OrderController;

// Route::get('/', function () {
//     return view('web.index');
// });
// Route::get('/order', function () {return view('web.order');});
Route::get('/unauthorized', function() {
    return view('errors.unauthorized');
})->name('unauthorized');



// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Customer order pages
// Route::get('/menu', [OrderController::class, 'menu'])->name('menu');
// Route::post('/order', [OrderController::class, 'store'])->name('order.store');


Route::get('/test-middleware', function () {
    return "Middleware loaded!";
})->middleware('role:admin');

