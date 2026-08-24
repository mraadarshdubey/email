<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RssFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'feed_url',
        'email_account_id',
        'recipient_tag_id',
        'is_active',
        'last_item_link',
        'last_checked_at',
        'sent_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function recipientTag(): BelongsTo
    {
        return $this->belongsTo(ContactTag::class, 'recipient_tag_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
