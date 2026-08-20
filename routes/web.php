<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/pharmacovigilance/login');
});

Route::get('/pharmacovigilance/{any?}', function () {
    return view('spa');
})->where('any', '.*');
