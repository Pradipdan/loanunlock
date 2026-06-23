<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiOtpController;
use App\Http\Controllers\Api\ApiApplicationController;
use App\Http\Controllers\Api\ApiPaymentController;
use App\Http\Controllers\Api\ApiUserController;

/*
|--------------------------------------------------------------------------
| LoanUnlock Mobile API Routes
|--------------------------------------------------------------------------
| All routes are prefixed with /api automatically by Laravel.
| Authentication uses Laravel Sanctum bearer tokens.
*/

// ── Public / Auth ─────────────────────────────────────────────────────────────
Route::prefix('otp')->group(function () {
    Route::post('/send',   [ApiOtpController::class, 'sendOtp']);
    Route::post('/verify', [ApiOtpController::class, 'verifyOtp']);
    Route::post('/resend', [ApiOtpController::class, 'resendOtp'])->middleware('auth:sanctum');
});

// ── Authenticated User Routes ─────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::get('/user/profile',    [ApiUserController::class, 'profile']);
    Route::post('/user/fcm-token', [ApiUserController::class, 'storeFcmToken']);
    Route::post('/auth/logout',    [ApiUserController::class, 'logout']);

    // Application steps
    Route::prefix('application')->group(function () {
        Route::get('/state',                [ApiApplicationController::class, 'getState']);
        Route::post('/personal',            [ApiApplicationController::class, 'savePersonal']);
        Route::post('/permissions',         [ApiApplicationController::class, 'savePermissions']);
        Route::post('/loan-details',        [ApiApplicationController::class, 'saveLoanDetails']);
        Route::get('/checking-score',       [ApiApplicationController::class, 'checkingScore']);
        Route::get('/pre-offer',            [ApiApplicationController::class, 'preOffer']);
        Route::get('/status',               [ApiApplicationController::class, 'status']);
        Route::post('/document',            [ApiApplicationController::class, 'uploadDocument']);
    });

    // Payment
    Route::prefix('payment')->group(function () {
        Route::post('/initiate', [ApiPaymentController::class, 'initiatePayment']);
        Route::post('/verify',   [ApiPaymentController::class, 'verifyPayment']);
        Route::get('/status',    [ApiPaymentController::class, 'paymentStatus']);
    });
});

// ── Webhooks (no auth) ────────────────────────────────────────────────────────
Route::post('/payment/razorpay-webhook', [ApiPaymentController::class, 'webhook']);
