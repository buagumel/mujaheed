<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VtuTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wallet_transaction_id',
        'reference',
        'service_type',
        'provider',
        'provider_reference',
        'plan_code',
        'plan_name',
        'recipient',
        'amount',
        'cost_price',
        'discount_amount',
        'fee',
        'status',
        'token',
        'customer_name',
        'units',
        'error_message',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'response_payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
