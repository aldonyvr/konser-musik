<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\KonserController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\registerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/cek-sesi-laravel', function () {
    $auth = Auth::check();
    return response()->json($auth);
});

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('users', [UserController::class, 'get']);
            Route::post('users', [UserController::class, 'index']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });
    });

    Route::prefix('konser')->group(function () {
        Route::get('', [KonserController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
        Route::post('', [KonserController::class, 'index']);
        Route::post('show', [KonserController::class, 'show']);
        Route::get('edit/{uuid}', [KonserController::class, 'edit'])->withoutMiddleware(['auth', 'verified']);
        Route::post('update/{uuid}', [KonserController::class, 'update']);
        Route::post('store', [KonserController::class, 'store']);
        Route::get('cities', [KonserController::class, 'getCities'])->withoutMiddleware(['auth', 'verified']);;
        Route::apiResource('konser', KonserController::class)
            ->except(['index', 'store']);
    });
    Route::prefix('tiket')->group(function () {
        Route::get('', [TiketController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
        Route::post('', [TiketController::class, 'index']);
        Route::post('store', [TiketController::class, 'store']);
        Route::post('update', [TiketController::class, 'update']);
        Route::apiResource('tiket', TiketController::class)
            ->except(['index', 'store']);
    });

    Route::prefix('order')->group(function () {
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
    });

    Route::prefix('register')->group(function () {
        Route::post('get', [RegisterController::class, 'get']);
        Route::post('', [RegisterController::class, 'index']);
        Route::post('show', [RegisterController::class, 'show']);
        Route::get('edit/{uuid}', [RegisterController::class, 'edit'])->withoutMiddleware(['auth', 'verified']);
        Route::post('update/{uuid}', [RegisterController::class, 'update']);
        Route::post('store', [RegisterController::class, 'store'])->withoutMiddleware(['auth', 'verified']);
        Route::apiResource('register', RegisterController::class)
            ->except(['index', 'store']);
    });
});
