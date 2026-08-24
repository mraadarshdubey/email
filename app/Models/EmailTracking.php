<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTracking extends Model
{
    use HasFactory;

    protected $table = 'email_tracking';

    protected $fillable = [
        'user_id',
        'batch_id',
        'email_account_id',
        'token',
        'recipient',
        'subject',
        'sent_at',
        'opened_at',
        'last_opened_at',
        'open_count',
        'last_open_ip',
        'last_open_user_agent',
        'clicked_at',
        'last_clicked_at',
        'click_count',
        'followed_up_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'followed_up_at' => 'datetime',
    ];

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(EmailTrackingClick::class);
    }

    public function isOpened(): bool
    {
        return !is_null($this->opened_at);
    }

    public function isClicked(): bool
    {
        return !is_null($this->clicked_at);
    }

    /**
     * Filter by a "batch key" as used in the Broadcasts/Follow-ups UI, which
     * is either a real batch_id or a synthetic "single-{id}" for legacy rows
     * sent before batch_id existed.
     */
    public function scopeForBatchKey($query, string $key)
    {
        if (str_starts_with($key, 'single-')) {
            return $query->whereNull('batch_id')->where('id', (int) substr($key, 7));
        }

        return $query->where('batch_id', $key);
    }
}
