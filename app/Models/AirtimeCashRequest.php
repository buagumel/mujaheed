<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirtimeCashRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'network',
        'amount',
        'exchange_rate_percent',
        'amount_to_receive',
        'sender_phone',
        'receiver_phone',
        'payout_method',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'reference',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate_percent' => 'decimal:2',
        'amount_to_receive' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
