<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleMeeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'branch_id',
        'employee_id',
        'to_address',
        'title',
        'date',
        'time',
        'note',
        'created_by',
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
