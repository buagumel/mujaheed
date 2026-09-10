<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'network',
        'airtime_discount_percent',
        'airtime_min_amount',
        'airtime_max_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'airtime_discount_percent' => 'decimal:2',
            'airtime_min_amount' => 'decimal:2',
            'airtime_max_amount' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
