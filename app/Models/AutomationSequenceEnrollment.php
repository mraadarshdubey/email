<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationSequenceEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence_id',
        'email_contact_id',
        'current_step',
        'status',
        'next_run_at',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(AutomationSequence::class, 'sequence_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(EmailContact::class, 'email_contact_id');
    }
}
