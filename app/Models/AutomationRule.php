<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'trigger_type',
        'trigger_tag_id',
        'action_type',
        'email_template_id',
        'email_account_id',
        'is_active',
        'runs_count',
        'last_run_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function triggerTag(): BelongsTo
    {
        return $this->belongsTo(ContactTag::class, 'trigger_tag_id');
    }

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

    public function triggerLabel(): string
    {
        return match ($this->trigger_type) {
            'contact_created' => 'Contact is created',
            'contact_tagged' => 'Contact tagged "' . ($this->triggerTag->name ?? '—') . '"',
            default => $this->trigger_type,
        };
    }

    public function actionLabel(): string
    {
        return match ($this->action_type) {
            'send_email' => 'Send "' . ($this->emailTemplate->name ?? '—') . '"',
            default => $this->action_type,
        };
    }
}
