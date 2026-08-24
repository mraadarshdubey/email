<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationSequenceStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence_id',
        'position',
        'delay_minutes',
        'email_template_id',
    ];

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(AutomationSequence::class, 'sequence_id');
    }

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }
}
