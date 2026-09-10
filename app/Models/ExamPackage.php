<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'standard_price',
        'reseller_price',
        'vip_price',
        'status',
        'description',
    ];

    protected $casts = [
        'standard_price' => 'decimal:2',
        'reseller_price' => 'decimal:2',
        'vip_price' => 'decimal:2',
    ];

    public function getPriceForTier(?string $tier): float
    {
        return match (strtolower($tier ?? 'standard')) {
            'vip' => (float) $this->vip_price,
            'reseller' => (float) $this->reseller_price,
            default => (float) $this->standard_price,
        };
    }
}
