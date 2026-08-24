<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'trigger_tag_id',
        'email_account_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function triggerTag(): BelongsTo
    {
        return $this->belongsTo(ContactTag::class, 'trigger_tag_id');
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(AutomationSequenceStep::class, 'sequence_id')->orderBy('position');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(AutomationSequenceEnrollment::class, 'sequence_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
