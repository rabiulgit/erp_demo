<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadBoq extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id', 
        'uploaded_by', 
        'file', 
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
}
