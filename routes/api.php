<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ApiAuthController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return User::with('customer')->find($request->user()->id);
});

Route::group(['prefix' => 'book'], function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/search', [BookController::class, 'search']);
    Route::middleware(['admin'])->post('/', [BookController::class, 'store']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::middleware(['admin'])->put('/{id}', [BookController::class, 'update']);
    Route::middleware(['admin'])->delete('/{id}', [BookController::class, 'destroy']);
});

Route::group(['prefix' => 'customer'], function () {
    Route::middleware(['admin'])->get('/', [CustomerController::class, 'index']);
    Route::middleware(['auth:sanctum'])->get('/{id}', [CustomerController::class, 'show']);
    Route::middleware(['auth:sanctum'])->put('/{id}', [CustomerController::class, 'update']);
    Route::middleware(['admin'])->delete('/{id}', [CustomerController::class, 'destroy']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login']);
    Route::middleware(['auth:sanctum'])->post('/logout', [ApiAuthController::class, 'logout']);
});

Route::get('unauthenticated', function () {
    return response()->json([
        'message' => 'unauthenticated',
        'endpoints' => [
            ['endpoint' => 'api/auth/login', 'method' => 'POST', 'shape' => [
                'email', 'password'
            ]], ['endpoint' => 'api/auth/register', 'method' => 'POST', 'shape' => [
                'name', 'email', 'password', 'address_line_1', 'address_line_2', 'phone_number', 'city', 'parish'
            ]],
            ['endpoint' => 'api/auth/logout', 'method' => 'POST', 'shape' => []]
        ]
    ], 401);
})->name('unauthenticated');
