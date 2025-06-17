<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TicketController;

// Auth
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('login',    [LoginController::class,   'showLoginForm'])->name('login');
    Route::post('login',   [LoginController::class,   'login']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Home: redirige al listado de tickets
Route::get('/', function () {
    return redirect()->route('tickets.index');
})->middleware('auth');

// Tickets
Route::middleware('auth')->group(function () {
    Route::get('tickets',        [TicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets',       [TicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{id}',   [TicketController::class, 'show'])->name('tickets.show');
    Route::post('tickets/{id}/advance', [TicketController::class, 'advance'])->name('tickets.advance');
});
