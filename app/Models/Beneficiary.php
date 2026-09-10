<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type',
        'identifier',
        'name_nickname',
        'network_or_disco',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
