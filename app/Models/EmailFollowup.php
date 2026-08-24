<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'source_batch_id',
        'source_subject',
        'condition',
        'wait_minutes',
        'email_template_id',
        'email_account_id',
        'is_active',
        'sent_count',
        'last_run_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function conditionLabel(): string
    {
        return $this->condition === 'not_opened' ? "Hasn't opened" : "Hasn't clicked";
    }

    public function waitLabel(): string
    {
        if ($this->wait_minutes % 1440 === 0) {
            $days = $this->wait_minutes / 1440;
            return $days . ' day' . ($days == 1 ? '' : 's');
        }
        if ($this->wait_minutes % 60 === 0) {
            $hours = $this->wait_minutes / 60;
            return $hours . ' hour' . ($hours == 1 ? '' : 's');
        }
        return $this->wait_minutes . ' min';
    }
}
