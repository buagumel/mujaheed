<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'sent_by',
        'title',
        'message',
        'target_audience',
        'channel',
        'recipients_count',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
