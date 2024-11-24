<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCause extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'employee_id',
        'date',
        'time',
        'note',
        'type',
        'created_by',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
