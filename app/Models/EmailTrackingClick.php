<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTrackingClick extends Model
{
    use HasFactory;

    protected $table = 'email_tracking_clicks';

    protected $fillable = [
        'email_tracking_id',
        'url',
        'ip_address',
        'user_agent',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function tracking(): BelongsTo
    {
        return $this->belongsTo(EmailTracking::class, 'email_tracking_id');
    }
}
