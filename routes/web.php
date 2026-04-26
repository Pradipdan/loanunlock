<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\User\ApplicationController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoanController;
use App\Http\Controllers\Admin\AdminUserController;

/*
|--------------------------------------------------------------------------
| Public / Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('splash');
});

// Fast2SMS website verification (for unlocking OTP route)
// Set FAST2SMS_VERIFY_TOKEN in .env with the token from Fast2SMS dashboard
Route::get('/fast2sms-verify', function () {
    $token = env('FAST2SMS_VERIFY_TOKEN', '');
    if (empty($token)) {
        abort(404);
    }
    return response($token, 200)->header('Content-Type', 'text/plain');
});

// Splash Screen
Route::get('/welcome', [ApplicationController::class, 'splash'])->name('splash');

// OTP Flow
Route::get('/verify', [OtpController::class, 'showMobile'])->name('otp.mobile');
Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('otp.send');
Route::get('/verify-otp', [OtpController::class, 'showOtp'])->name('otp.verify.form');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/resend-otp', [OtpController::class, 'resendOtp'])->name('otp.resend');

Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.otp'])->group(function () {

    // Step 1 – Personal Details
    Route::get('/personal-details', [ApplicationController::class, 'personalDetails'])->name('application.personal');
    Route::post('/personal-details', [ApplicationController::class, 'savePersonal'])->name('application.personal.save');

    // Step 2 – Permissions
    Route::get('/permissions', [ApplicationController::class, 'permissions'])->name('application.permissions');
    Route::post('/permissions', [ApplicationController::class, 'savePermissions'])->name('application.permissions.save');

    // Step 3 – Loan Personalization
    Route::get('/loan-details', [ApplicationController::class, 'loanDetails'])->name('application.loan');
    Route::post('/loan-details', [ApplicationController::class, 'saveLoanDetails'])->name('application.loan.save');

    // Step 3.5 – Checking Credit Score (Simulation/Transition)
    Route::get('/checking-score', [ApplicationController::class, 'checkingScore'])->name('application.checking');

    // Step 4 – Pre-Approved Offer (Payment Wall)
    Route::get('/pre-approved-offer', [ApplicationController::class, 'preApprovedOffer'])->name('application.pre_offer');

    // Step 5 – Payment / Unlock (Razorpay)
    Route::get('/unlock-loan', [PaymentController::class, 'showUnlock'])->name('payment.unlock');
    Route::post('/initiate-payment', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::post('/verify-payment', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
    Route::get('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment-cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');

    // Legacy eligibility routes (kept for back-compat)
    Route::get('/eligibility', [ApplicationController::class, 'eligibility'])->name('application.eligibility');
    Route::post('/check-eligibility', [ApplicationController::class, 'checkEligibility'])->name('application.check.eligibility');

    // Step 6 – Final / Dashboard (requires successful payment)
    Route::middleware(['require.payment'])->group(function () {
        Route::get('/application-status', [DashboardController::class, 'status'])->name('application.status');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

        // Documents upload
        Route::post('/upload-document', [ApplicationController::class, 'uploadDocument'])->name('application.upload');
    });
});

// Razorpay Webhook
Route::post('/razorpay/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// Cosmofeed Webhook & Initiation (Keep for now or remove if requested, but user said remove Stripe)
Route::get('/cosmofeed/initiate', [PaymentController::class, 'initiateCosmofeed'])->name('payment.cosmofeed.initiate')->middleware('auth.otp');
Route::post('/cosmofeed/webhook', [PaymentController::class, 'webhookCosmofeed'])->name('payment.cosmofeed.webhook');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin Auth
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::get('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Protected
    Route::middleware(['admin.auth'])->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.home');

        // Loan Management
        Route::get('/loans', [AdminLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/{id}', [AdminLoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{id}/approve', [AdminLoanController::class, 'approve'])->name('loans.approve');
        Route::post('/loans/{id}/reject', [AdminLoanController::class, 'reject'])->name('loans.reject');
        Route::post('/loans/{id}/disburse', [AdminLoanController::class, 'disburse'])->name('loans.disburse');
        Route::get('/loans/{id}/documents', [AdminLoanController::class, 'documents'])->name('loans.documents');
        Route::post('/loans/{id}/note', [AdminLoanController::class, 'addNote'])->name('loans.note');

        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{id}/block', [AdminUserController::class, 'block'])->name('users.block');
        Route::post('/users/{id}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');

        // Reports
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [AdminDashboardController::class, 'exportCsv'])->name('reports.export');

        // Settings
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminDashboardController::class, 'saveSettings'])->name('settings.save');
    });
});
