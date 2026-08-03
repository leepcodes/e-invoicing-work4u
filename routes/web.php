<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;



Route::get('/', [LandingController::class, 'index'])
    ->name('landing');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});


require __DIR__.'/settings.php';
