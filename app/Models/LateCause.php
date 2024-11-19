<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateCause extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'employee_id',
        'date',
        'time',
        'note',
        'created_by',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
