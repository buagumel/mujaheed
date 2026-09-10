<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'network',
        'provider',
        'name',
        'code',
        'type',
        'size',
        'validity',
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

    public function scopeByNetwork($query, string $network)
    {
        return $query->where('network', strtoupper($network));
    }
}
