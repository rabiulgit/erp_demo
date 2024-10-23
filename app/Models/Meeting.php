<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'branch_id',
        'to_adddress',
        'department_id',
        'employee_id',
        'title',
        'date',
        'time',
        'note',
        'created_by',
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }
}
