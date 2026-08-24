<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'headline',
        'description',
        'fields_config',
        'tag_id',
        'success_message',
        'is_active',
        'submissions_count',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(ContactTag::class, 'tag_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(LeadFormSubmission::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasField(string $key): bool
    {
        return in_array($key, $this->fields_config ?? [], true);
    }
}
