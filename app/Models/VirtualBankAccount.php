<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'account_name',
        'customer_name',
        'provider',
        'identity_type',
        'license_number',
        'reference',
        'account_reference',
        'status',
        'total_funded',
        'last_funded_at',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'total_funded' => 'decimal:2',
        'last_funded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
