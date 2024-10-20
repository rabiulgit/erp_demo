<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'checklist',
        'report',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
