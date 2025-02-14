<?php

use App\Models\Konser;
use App\Models\DataPemesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\KonserController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\registerController;
use App\Http\Controllers\DataPemesananController;
use App\Http\Controllers\TicketScannerController;


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

Route::get('/getKonser', [DataPemesananController::class, 'getAllKonser']);

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me'])->withoutMiddleware('auth');
    Route::delete('destroy', [AuthController::class, 'destroy'])->withoutMiddleware('auth');

    Route::post('send-otp', [AuthController::class, 'sendUserOTP'])->withoutMiddleware('auth');
    Route::post('verify-otp', [AuthController::class, 'verifyUserOTP'])->withoutMiddleware('auth');
    Route::post('reset-user-password', [AuthController::class, 'resetUserPassword'])->withoutMiddleware('auth');
    Route::post('complete-registration', [AuthController::class, 'completeRegistration'])->withoutMiddleware('auth');
    Route::post('initialize-registration', [AuthController::class, 'initializeRegistration'])->withoutMiddleware('auth');
    Route::post('verify-registration-otp', [AuthController::class, 'verifyRegistrationOTP'])->withoutMiddleware('auth');
    Route::post('change-password', [AuthController::class, 'changePassword'])->withoutMiddleware('auth');
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
                Route::get('destroy/{uuid}', [UserController::class, 'destroy'])->withoutMiddleware('can:master-user');
                Route::post('users/store', [UserController::class, 'store'])->withoutMiddleware('can:master-user');
                Route::put('change-password', [UserController::class, 'changePassword'])->withoutMiddleware('can:master-user');
                Route::apiResource('users', UserController::class)
                    ->except(['index', 'store'])->scoped(['user' => 'uuid'])->withoutMiddleware('can:master-user');
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
            Route::post('get', [KonserController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::post('', [KonserController::class, 'index'])->withoutMiddleware(['auth', 'verified']);
            Route::get('', [KonserController::class, 'index'])->withoutMiddleware(['auth', 'verified']);
            Route::post('show', [KonserController::class, 'show']);
            Route::get('edit/{uuid}', [KonserController::class, 'edit'])->withoutMiddleware(['auth', 'verified']);
            Route::post('update/{uuid}', [KonserController::class, 'update']);
            Route::post('store', [KonserController::class, 'store']);
            Route::get('cities', [KonserController::class, 'getCities'])->withoutMiddleware(['auth', 'verified']);;
            Route::apiResource('konser', KonserController::class)
                ->except(['index', 'store']);
        });
        Route::prefix('tiket')->group(function () {
            Route::get('get', [TiketController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::post('', [TiketController::class, 'index'])->withoutMiddleware(['auth', 'verified']);
            Route::get('edit/{uuid}', [TiketController::class, 'edit'])->withoutMiddleware(['auth', 'verified']);
            Route::get('show/{uuid}', [TiketController::class, 'show'])->withoutMiddleware(['auth', 'verified']);
            Route::post('store', [TiketController::class, 'store']);
            Route::post('update/{uuid}', [TiketController::class, 'update'])
                ->withoutMiddleware(['auth', 'verified'])
                ->name('tiket.update');
            // Route::apiResource('tiket', TiketController::class)
            //     ->except(['index', 'store']);
        });

        Route::prefix('order')->group(function () {
            Route::resource('orders', OrderController::class)->only(['index', 'show']);
        });

        Route::prefix('banner')->group(function () {
            Route::get('', [BannerController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::post('', [BannerController::class, 'index']);
            Route::post('show', [BannerController::class, 'show']);
            Route::get('edit/{uuid}', [BannerController::class, 'edit'])->withoutMiddleware(['auth', 'verified']);
            Route::post('update/{uuid}', [BannerController::class, 'update'])->withoutMiddleware(['auth', 'verified']);
            Route::delete('destroy/{uuid}', [BannerController::class, 'destroy'])->withoutMiddleware(['auth', 'verified']);
            Route::post('store', [BannerController::class, 'store']);
            Route::apiResource('banner', BannerController::class)
                ->except(['index', 'store']);
        });

        Route::prefix('datapemesan')->group(function () {
            Route::get('get', [DataPemesananController::class, 'get'])->withoutMiddleware(['auth', 'verified']);
            Route::get('getAllKonser', [DataPemesananController::class, 'getAllKonser'])->withoutMiddleware(['auth', 'verified']);
            Route::post('', [DataPemesananController::class, 'index'])->withoutMiddleware(['auth', 'verified']);
            Route::post('show', [DataPemesananController::class, 'show']);
            Route::get('edit/{uuid}', [DataPemesananController::class, 'edit']);
            Route::post('update/{uuid}', [DataPemesananController::class, 'update']);
            Route::post('store', [DataPemesananController::class, 'store'])->withoutMiddleware(['auth', 'verified']);
            Route::get('/purchased-tickets', [DataPemesananController::class, 'getPurchasedTickets'])->name('purchased-tickets')->withoutMiddleware(['auth', 'verified']);
            Route::post('/payment-callback', [DataPemesananController::class, 'handlePaymentCallback'])->name('payment-callback')->withoutMiddleware(['auth', 'verified']);
            Route::post('/handle-expired-payment', [DataPemesananController::class, 'handleExpiredPayment']);
        });
        Route::prefix('scan')->group(function () {
            Route::post('/scan-ticket', [ScannerController::class, 'scanTicket']);
        });

        Route::middleware(['auth:sanctum', 'role:concert_admin'])->group(function () {
            Route::post('/ticket/verify-scan', [TicketScannerController::class, 'verifyScan']);
        });

        Route::middleware(['auth:sanctum', 'role:scanner'])->group(function () {
            Route::post('/tickets/scan', [TicketScannerController::class, 'scan']);
            Route::get('/tickets/scan-history', [TicketScannerController::class, 'getScanHistory']);
        });
    });

Route::middleware(['auth:api'])->group(function () {
    // Routes that both admin and mitra can access
    Route::middleware(['mitra.user'])->group(function () {
        Route::get('/master/users', [UserController::class, 'get']);
        Route::post('/master/users', [UserController::class, 'index']);
    });
});
