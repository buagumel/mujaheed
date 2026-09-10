<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'tier',
        'referral_code',
        'referred_by_id',
        'referral_balance',
        'email_verified_at',
        'transaction_pin',
        'pin_attempts',
        'pin_locked_until',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'transaction_pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'pin_locked_until' => 'datetime',
            'password' => 'hashed',
            'referral_balance' => 'decimal:2',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function isReseller(): bool
    {
        return in_array(strtolower($this->tier ?? 'standard'), ['reseller', 'vip']);
    }

    public function isVip(): bool
    {
        return strtolower($this->tier ?? 'standard') === 'vip';
    }

    public function getTierDisplayName(): string
    {
        return match (strtolower($this->tier ?? 'standard')) {
            'vip' => 'VIP Partner',
            'reseller' => 'Reseller Agent',
            default => 'Smart Earner',
        };
    }

    public function hasTransactionPin(): bool
    {
        return !empty($this->transaction_pin);
    }

    public function isPinLocked(): bool
    {
        if ($this->pin_locked_until && $this->pin_locked_until->isFuture()) {
            return true;
        }

        return false;
    }

    public function verifyPin(string $pin): bool
    {
        if ($this->isPinLocked()) {
            return false;
        }

        if (!$this->transaction_pin) {
            return false;
        }

        if (Hash::check($pin, $this->transaction_pin)) {
            $this->update([
                'pin_attempts' => 0,
                'pin_locked_until' => null,
            ]);
            return true;
        }

        $attempts = $this->pin_attempts + 1;
        $lockedUntil = null;

        if ($attempts >= 5) {
            $lockedUntil = now()->addMinutes(30);
            $attempts = 0;
        }

        $this->update([
            'pin_attempts' => $attempts,
            'pin_locked_until' => $lockedUntil,
        ]);

        return false;
    }

    public function setTransactionPin(string $pin): void
    {
        $this->update([
            'transaction_pin' => Hash::make($pin),
            'pin_attempts' => 0,
            'pin_locked_until' => null,
        ]);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function vtuTransactions(): HasMany
    {
        return $this->hasMany(VtuTransaction::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function virtualBankAccounts(): HasMany
    {
        return $this->hasMany(VirtualBankAccount::class);
    }

    public function examPinTransactions(): HasMany
    {
        return $this->hasMany(ExamPinTransaction::class);
    }

    public function airtimeCashRequests(): HasMany
    {
        return $this->hasMany(AirtimeCashRequest::class);
    }

    public function referralCommissions(): HasMany
    {
        return $this->hasMany(ReferralCommission::class);
    }

    public function referredUsers(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet()->firstOrCreate([], [
            'balance' => 0.00,
            'currency' => 'NGN',
            'status' => 'active',
        ]);
    }

    public function getOrCreateReferralCode(): string
    {
        if (empty($this->referral_code)) {
            $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $this->name ?? 'VTU'), 0, 4) . rand(100, 999));
            $this->update(['referral_code' => $code]);
            return $code;
        }

        return $this->referral_code;
    }

    public function getOrCreateVirtualAccounts(): \Illuminate\Database\Eloquent\Collection
    {
        $accounts = $this->virtualBankAccounts()->where('status', 'active')->get();

        if ($accounts->isEmpty()) {
            try {
                $payrantService = app(\App\Services\Payment\PayrantService::class);
                $payrantService->createVirtualAccount($this);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Error creating Payrant virtual account for user ' . $this->id . ': ' . $e->getMessage());
            }

            $accounts = $this->virtualBankAccounts()->where('status', 'active')->get();
        }

        return $accounts;
    }
}
