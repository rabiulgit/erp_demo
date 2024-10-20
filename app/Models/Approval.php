<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'module',
        'role_id',
        'order', 
    ];
    public function role_data(){
        return $this->belongsTo(Role::class,'role_id','id');
    }

    public function role()
    {
        return $this->belongsTo('Spatie\Permission\Models\Role', 'role_id');
    }

}
