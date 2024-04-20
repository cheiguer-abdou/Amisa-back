<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});


// Route::post('/login', 'AuthController@login');
// // Route::post('/register', 'AuthController@register')->name('register');
// Route::middleware('auth:api')->post('/logout', 'AuthController@logout');
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->employer();
// });
