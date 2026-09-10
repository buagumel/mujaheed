<?php

use App\Http\Controllers\Admin\AdminAirtimeCashController;
use App\Http\Controllers\Admin\AdminAirtimeController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminBroadcastController;
use App\Http\Controllers\Admin\AdminCableController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminElectricityController;
use App\Http\Controllers\Admin\AdminExamPinController;
use App\Http\Controllers\Admin\AdminPricingController;
use App\Http\Controllers\Admin\AdminProviderController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminSystemController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVirtualAccountController;
use App\Http\Controllers\Admin\AdminWalletController;
use App\Http\Controllers\Payment\PayrantWebhookController;
use App\Http\Controllers\Airtime\AirtimeController;
use App\Http\Controllers\AirtimeCash\AirtimeCashController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\BeneficiaryController;
use App\Http\Controllers\User\ReceiptController;
use App\Http\Controllers\Cable\CableController;
use App\Http\Controllers\Data\DataController;
use App\Http\Controllers\Electricity\ElectricityController;
use App\Http\Controllers\Exam\ExamPinController;
use App\Http\Controllers\Referral\ReferralController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SecurityController;
use App\Http\Controllers\User\SupportController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\UserTier\UserTierController;
use App\Http\Controllers\Wallet\WalletController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Dynamic Landing Page & Legal Routes
Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/privacy', [LandingPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [LandingPageController::class, 'terms'])->name('terms');

// Web-Accessible cPanel Cache Clearing Route
Route::get('/clear-cpanel-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    return response('<h1>✅ cPanel Laravel Cache Cleared Successfully!</h1><p>View, route, config, and framework caches have been wiped. Refresh <a href="/">https://bjdatasub.com</a> now.</p>');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email');

    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPasswordWithOtp'])->name('password.update');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('verification.notice');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/verify-email/resend', [AuthController::class, 'resendVerificationOtp'])->name('verification.resend');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Webhook handling (excluded from CSRF)
Route::post('/webhooks/payment', [WalletController::class, 'webhook'])->name('webhook.payment');
Route::post('/webhook/payrant', [PayrantWebhookController::class, 'handle'])->name('webhook.payrant');

// Customer Protected Routes
Route::middleware(['auth', 'active_user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Wallet & Dedicated Bank Accounts
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/fund', [WalletController::class, 'showFund'])->name('wallet.fund');
    Route::post('/wallet/fund', [WalletController::class, 'initiateFunding'])->name('wallet.fund.initiate');
    Route::get('/wallet/fund/mock/{reference}', [WalletController::class, 'mockCheckout'])->name('wallet.fund.mock.checkout');
    Route::post('/wallet/fund/mock/{reference}', [WalletController::class, 'completeMockCheckout'])->name('wallet.fund.mock.complete');
    Route::get('/wallet/fund/callback', [WalletController::class, 'callback'])->name('wallet.fund.callback');

    // Airtime
    Route::get('/airtime', [AirtimeController::class, 'index'])->name('airtime.index');
    Route::post('/airtime/purchase', [AirtimeController::class, 'purchase'])->name('airtime.purchase');

    // Data
    Route::get('/data', [DataController::class, 'index'])->name('data.index');
    Route::get('/data/plans', [DataController::class, 'getPlansByNetwork'])->name('data.plans');
    Route::post('/data/purchase', [DataController::class, 'purchase'])->name('data.purchase');

    // Electricity
    Route::get('/electricity', [ElectricityController::class, 'index'])->name('electricity.index');
    Route::post('/electricity/verify', [ElectricityController::class, 'verifyMeter'])->name('electricity.verify');
    Route::post('/electricity/pay', [ElectricityController::class, 'pay'])->name('electricity.pay');

    // Cable TV
    Route::get('/cable', [CableController::class, 'index'])->name('cable.index');
    Route::get('/cable/plans', [CableController::class, 'getPlansByProvider'])->name('cable.plans');
    Route::post('/cable/verify', [CableController::class, 'verifySmartcard'])->name('cable.verify');
    Route::post('/cable/pay', [CableController::class, 'pay'])->name('cable.pay');

    // Exam Scratch Cards & PINs (WAEC, NECO, NABTEB, JAMB)
    Route::get('/exam-pins', [ExamPinController::class, 'index'])->name('exam.index');
    Route::post('/exam-pins/purchase', [ExamPinController::class, 'purchase'])->name('exam.purchase');
    Route::get('/exam-pins/view/{reference}', [ExamPinController::class, 'show'])->name('exam.show');
    Route::get('/exam-pins/receipt/{reference}', [ExamPinController::class, 'receipt'])->name('exam.receipt');

    // Membership Tiers & Wholesale Upgrades
    Route::get('/membership-tier', [UserTierController::class, 'index'])->name('tier.index');
    Route::post('/membership-tier/upgrade', [UserTierController::class, 'upgrade'])->name('tier.upgrade');

    // Referral Program
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::post('/referrals/withdraw', [ReferralController::class, 'withdraw'])->name('referrals.withdraw');

    // Airtime to Cash (Airtime Swap)
    Route::get('/airtime-to-cash', [AirtimeCashController::class, 'index'])->name('airtime_cash.index');
    Route::post('/airtime-to-cash', [AirtimeCashController::class, 'store'])->name('airtime_cash.store');
    Route::get('/airtime-to-cash/order/{reference}', [AirtimeCashController::class, 'show'])->name('airtime_cash.show');

    // Transactions & Receipts
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{reference}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{reference}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');

    // Saved Beneficiaries
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->name('beneficiaries.index');
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::delete('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_read');

    // Profile & Security (with Multi-layer OTP)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/security', [SecurityController::class, 'index'])->name('security.index');
    Route::post('/security/verify-pin', [SecurityController::class, 'verifyPinAjax'])->name('security.pin.verify_ajax');
    Route::post('/security/send-otp', [SecurityController::class, 'sendOtp'])->name('security.send_otp');
    Route::post('/security/pin', [SecurityController::class, 'setPin'])->name('security.pin.set');
    Route::post('/security/change-pin', [SecurityController::class, 'changePin'])->name('security.pin.change');
    Route::post('/security/reset-pin', [SecurityController::class, 'resetPinWithOtp'])->name('security.pin.reset');
    Route::post('/security/password', [SecurityController::class, 'updatePassword'])->name('security.password.update');

    // Customer Support & Tickets
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/support/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/support/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/support/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/support/tickets/{id}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/support/tickets/{id}/close', [TicketController::class, 'close'])->name('tickets.close');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('login.post');
    });

    Route::post('/logout', [\App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->middleware('auth:admin')->name('logout');
});

// Admin Protected Routes
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset_password');

    // Wallets
    Route::get('/wallets', [AdminWalletController::class, 'index'])->name('wallets.index');
    Route::post('/wallets/{wallet}/adjust', [AdminWalletController::class, 'adjust'])->name('wallets.adjust');

    // Transactions
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [AdminTransactionController::class, 'show'])->name('transactions.show');
    Route::post('/transactions/{transaction}/retry-status', [AdminTransactionController::class, 'retryStatus'])->name('transactions.retry_status');
    Route::post('/transactions/{transaction}/refund', [AdminTransactionController::class, 'refund'])->name('transactions.refund');

    // Airtime Settings
    Route::get('/airtime', [AdminAirtimeController::class, 'index'])->name('airtime.index');
    Route::post('/airtime/{network}', [AdminAirtimeController::class, 'update'])->name('airtime.update');

    // Data Plans
    Route::get('/data', [AdminDataController::class, 'index'])->name('data.index');
    Route::get('/data/provider-plans', [AdminDataController::class, 'getProviderPlans'])->name('data.provider_plans');
    Route::post('/data', [AdminDataController::class, 'store'])->name('data.store');
    Route::put('/data/{plan}', [AdminDataController::class, 'update'])->name('data.update');
    Route::delete('/data/{plan}', [AdminDataController::class, 'destroy'])->name('data.destroy');

    // Electricity
    Route::get('/electricity', [AdminElectricityController::class, 'index'])->name('electricity.index');
    Route::post('/electricity/{provider}', [AdminElectricityController::class, 'update'])->name('electricity.update');

    // Wallets & Dedicated Virtual Accounts
    Route::get('/wallets', [AdminWalletController::class, 'index'])->name('wallets.index');
    Route::post('/wallets/{user}/credit', [AdminWalletController::class, 'credit'])->name('wallets.credit');
    Route::post('/wallets/{user}/debit', [AdminWalletController::class, 'debit'])->name('wallets.debit');
    Route::get('/virtual-accounts', [AdminVirtualAccountController::class, 'index'])->name('virtual-accounts.index');
    Route::get('/virtual-accounts/{virtualAccount}', [AdminVirtualAccountController::class, 'show'])->name('virtual-accounts.show');
    Route::post('/virtual-accounts/{virtualAccount}/reconcile', [AdminVirtualAccountController::class, 'reconcile'])->name('virtual-accounts.reconcile');
    Route::post('/virtual-accounts/{user}/regenerate', [AdminVirtualAccountController::class, 'regenerate'])->name('virtual-accounts.regenerate');

    // Cable TV
    Route::get('/cable', [AdminCableController::class, 'index'])->name('cable.index');
    Route::post('/cable', [AdminCableController::class, 'store'])->name('cable.store');
    Route::put('/cable/{plan}', [AdminCableController::class, 'update'])->name('cable.update');

    // Exam PINs Management
    Route::get('/exam-pins', [AdminExamPinController::class, 'index'])->name('exam_pins.index');
    Route::post('/exam-pins/{id}', [AdminExamPinController::class, 'updatePrice'])->name('exam_pins.update');

    // Airtime to Cash Management
    Route::get('/airtime-to-cash', [AdminAirtimeCashController::class, 'index'])->name('airtime_cash.index');
    Route::post('/airtime-to-cash/rates', [AdminAirtimeCashController::class, 'updateRates'])->name('airtime_cash.rates');
    Route::post('/airtime-to-cash/{id}/approve', [AdminAirtimeCashController::class, 'approve'])->name('airtime_cash.approve');
    Route::post('/airtime-to-cash/{id}/reject', [AdminAirtimeCashController::class, 'reject'])->name('airtime_cash.reject');

    // Support Helpdesk & Tickets
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{id}', [AdminTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{id}/status', [AdminTicketController::class, 'updateStatus'])->name('tickets.status');

    // Pricing & Financial Analytics
    Route::get('/pricing', [AdminPricingController::class, 'index'])->name('pricing.index');
    Route::get('/pricing/export-profit', [AdminPricingController::class, 'exportProfitCsv'])->name('pricing.export_profit');

    // Coupons & Promo Codes
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // Broadcast Engine
    Route::get('/broadcast', [AdminBroadcastController::class, 'index'])->name('broadcast.index');
    Route::post('/broadcast', [AdminBroadcastController::class, 'send'])->name('broadcast.send');

    // Providers & Settings
    Route::get('/providers', [AdminProviderController::class, 'index'])->name('providers.index');
    Route::post('/providers', [AdminProviderController::class, 'update'])->name('providers.update');
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [AdminSettingController::class, 'testEmail'])->name('settings.test_email');

    // System Logs & Backups
    Route::get('/system/logs', [AdminSystemController::class, 'logs'])->name('system.logs');
    Route::post('/system/logs/clear', [AdminSystemController::class, 'clearLogs'])->name('system.logs.clear');
    Route::get('/system/backup', [AdminSystemController::class, 'backupDatabase'])->name('system.backup');

    // Audit Logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit.index');
});

// Standalone Printable Receipt
Route::get('/receipt/{reference}', [ReceiptController::class, 'show'])->name('receipt.show');
