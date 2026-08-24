<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempMailAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'email',
        'first_name',
        'last_name',
        'company',
        'phone',
        'notes',
    ];
}
