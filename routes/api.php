<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/price', [ProductController::class, 'productsPrice']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/p/search', [ProductController::class, 'searchProducts']);

Route::get('/employees', [EmployerController::class, 'index']);
Route::get('/employees/{id}', [EmployerController::class, 'show']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/check-session', [AuthController::class, 'checkSession']);
Route::post('/employees/{id}', [EmployerController::class, 'update']);
Route::delete('/employees/{id}', [EmployerController::class, 'destroy']);
Route::get('/u/search', [EmployerController::class, 'searchEmployees']);

Route::get('/clients', [ClientController::class, 'index']);
Route::get('/clients/{id}', [ClientController::class, 'show']);
Route::post('/clients', [ClientController::class, 'store']);
Route::post('/clients/{id}', [ClientController::class, 'update']);
Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
Route::get('/clients/{clientId}/products', [ProductController::class, 'indexByClient']);
Route::get('/c/count', [ClientController::class, 'getClientsCount']);
Route::get('/c/search', [ClientController::class, 'searchClients']);

Route::post('/order', [OrderController::class, 'order']);
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders/{id}', [OrderController::class, 'update']);
Route::get('/o/search', [OrderController::class, 'searchOrders']);
Route::get('/orders/sales', [OrderController::class, 'salesPerYear']);

Route::get('/budgets', [BudgetController::class, 'index']);
