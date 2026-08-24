<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_form_id',
        'email_contact_id',
        'ip_address',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(EmailContact::class, 'email_contact_id');
    }
}
