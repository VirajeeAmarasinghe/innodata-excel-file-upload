<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('import');
});

Route::post('/import', [UserController::class, 'import']);
