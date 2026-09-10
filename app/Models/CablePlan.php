<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CablePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'name',
        'code',
        'provider_price',
        'selling_price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'provider_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', strtoupper($provider));
    }
}
