<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('import.index');
Route::post('/import', [UserController::class, 'import'])->name('import.import');
