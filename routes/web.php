<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemCheck;


Route::get('/', function () {
    return view('welcome');
});



Route::get('/system-check', [SystemCheck::class, 'main']);

