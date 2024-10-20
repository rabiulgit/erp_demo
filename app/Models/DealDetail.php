<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'client_name',
        'email',
        'phone',
        'company_code',
        'address',
        'informations',
    ];

}
