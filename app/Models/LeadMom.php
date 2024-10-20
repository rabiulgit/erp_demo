<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadMom extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'attendee',
        'topic',
        'place_time',
        'next_plan',
        'interaction',
    ];

}
