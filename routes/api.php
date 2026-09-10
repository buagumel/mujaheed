<?php

use App\Http\Controllers\Api\ApiAirtimeCashController;
use App\Http\Controllers\Api\ApiAirtimeController;
use App\Http\Controllers\Api\ApiAppConfigController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiBeneficiaryController;
use App\Http\Controllers\Api\ApiCableController;
use App\Http\Controllers\Api\ApiCouponController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ApiDataController;
use App\Http\Controllers\Api\ApiElectricityController;
use App\Http\Controllers\Api\ApiExamPinController;
use App\Http\Controllers\Api\ApiNotificationController;
use App\Http\Controllers\Api\ApiProfileController;
use App\Http\Controllers\Api\ApiReferralController;
use App\Http\Controllers\Api\ApiSupportController;
use App\Http\Controllers\Api\ApiTransactionController;
use App\Http\Controllers\Api\ApiUserTierController;
use App\Http\Controllers\Api\ApiWalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (NativePHP / Mobile / External)
|--------------------------------------------------------------------------
*/

// Public Endpoints
Route::get('/app/config', [ApiAppConfigController::class, 'config']);
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/forgot-password', [ApiAuthController::class, 'forgotPassword']);
Route::post('/reset-password', [ApiAuthController::class, 'resetPassword']);

// Authenticated Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);
    Route::get('/dashboard', [ApiDashboardController::class, 'index']);

    // Wallet
    Route::get('/wallet', [ApiWalletController::class, 'index']);
    Route::get('/wallet/transactions', [ApiWalletController::class, 'transactions']);
    Route::post('/wallet/fund', [ApiWalletController::class, 'fund']);

    // Airtime
    Route::get('/airtime/networks', [ApiAirtimeController::class, 'networks']);
    Route::post('/airtime/purchase', [ApiAirtimeController::class, 'purchase']);

    // Data
    Route::get('/data/networks', [ApiDataController::class, 'networks']);
    Route::get('/data/plans', [ApiDataController::class, 'plans']);
    Route::post('/data/purchase', [ApiDataController::class, 'purchase']);

    // Electricity
    Route::get('/electricity/providers', [ApiElectricityController::class, 'providers']);
    Route::post('/electricity/verify', [ApiElectricityController::class, 'verify']);
    Route::post('/electricity/pay', [ApiElectricityController::class, 'pay']);

    // Cable TV
    Route::get('/cable/plans', [ApiCableController::class, 'plans']);
    Route::post('/cable/verify', [ApiCableController::class, 'verify']);
    Route::post('/cable/pay', [ApiCableController::class, 'pay']);

    // Exam PINs
    Route::get('/exam-pins/packages', [ApiExamPinController::class, 'packages']);
    Route::post('/exam-pins/purchase', [ApiExamPinController::class, 'purchase']);

    // Airtime to Cash
    Route::get('/airtime-to-cash/rates', [ApiAirtimeCashController::class, 'rates']);
    Route::post('/airtime-to-cash/submit', [ApiAirtimeCashController::class, 'submit']);
    Route::get('/airtime-to-cash/history', [ApiAirtimeCashController::class, 'history']);

    // Tiers
    Route::get('/tiers', [ApiUserTierController::class, 'index']);
    Route::post('/tiers/upgrade', [ApiUserTierController::class, 'upgrade']);

    // Referrals
    Route::get('/referrals', [ApiReferralController::class, 'index']);
    Route::post('/referrals/withdraw', [ApiReferralController::class, 'withdraw']);

    // Transactions & Receipts
    Route::get('/transactions', [ApiTransactionController::class, 'index']);
    Route::get('/transactions/{reference}', [ApiTransactionController::class, 'show']);

    // Notifications
    Route::get('/notifications', [ApiNotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [ApiNotificationController::class, 'markAllAsRead']);

    // Profile & Security
    Route::post('/profile', [ApiProfileController::class, 'update']);
    Route::post('/profile/password', [ApiProfileController::class, 'updatePassword']);
    Route::post('/profile/pin', [ApiProfileController::class, 'setPin']);
    Route::post('/profile/pin/change', [ApiProfileController::class, 'changePin']);
    Route::post('/profile/pin/send-otp', [ApiProfileController::class, 'sendPinResetOtp']);
    Route::post('/profile/pin/reset-otp', [ApiProfileController::class, 'resetPinWithOtp']);

    // Support
    Route::get('/support/tickets', [ApiSupportController::class, 'index']);
    Route::post('/support/tickets', [ApiSupportController::class, 'store']);
    Route::get('/support/tickets/{id}', [ApiSupportController::class, 'show']);
    Route::post('/support/tickets/{id}/reply', [ApiSupportController::class, 'reply']);

    // Saved Beneficiaries
    Route::get('/beneficiaries', [ApiBeneficiaryController::class, 'index']);
    Route::post('/beneficiaries', [ApiBeneficiaryController::class, 'store']);
    Route::delete('/beneficiaries/{beneficiary}', [ApiBeneficiaryController::class, 'destroy']);

    // Coupons
    Route::post('/coupons/verify', [ApiCouponController::class, 'verify']);
});
