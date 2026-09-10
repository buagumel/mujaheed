<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'service_type',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid for a given user, amount and service type
     */
    public function isValidFor(User $user, float $amount, string $serviceType): array
    {
        if ($this->status !== 'active') {
            return ['valid' => false, 'message' => 'This coupon code is no longer active.'];
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return ['valid' => false, 'message' => 'This coupon is not yet active.'];
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return ['valid' => false, 'message' => 'This coupon code has expired.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'This coupon has reached its maximum usage limit.'];
        }

        if ($this->service_type !== 'all' && $this->service_type !== $serviceType) {
            return ['valid' => false, 'message' => "This coupon is only valid for {$this->service_type} purchases."];
        }

        if ($amount < $this->min_order_amount) {
            return ['valid' => false, 'message' => "Minimum order amount to use this coupon is ₦" . number_format($this->min_order_amount, 2)];
        }

        // Check if user has already used this coupon
        $alreadyUsed = $this->usages()->where('user_id', $user->id)->exists();
        if ($alreadyUsed) {
            return ['valid' => false, 'message' => 'You have already redeemed this coupon code.'];
        }

        // Calculate discount
        $discount = 0.0;
        if ($this->discount_type === 'percentage') {
            $discount = ($this->discount_value / 100.0) * $amount;
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }
        } else {
            $discount = min((float) $this->discount_value, $amount);
        }

        return [
            'valid' => true,
            'discount' => round($discount, 2),
            'coupon' => $this,
            'message' => 'Coupon code applied successfully!',
        ];
    }
}
