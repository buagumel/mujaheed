<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamPinTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_code',
        'quantity',
        'unit_price',
        'total_amount',
        'pins_data',
        'reference',
        'status',
    ];

    protected $casts = [
        'pins_data' => 'array',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
