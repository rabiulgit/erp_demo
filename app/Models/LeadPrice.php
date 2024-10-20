<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'created_by',
        'cad',
        'boq',
        'description',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
