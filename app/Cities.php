<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $table = 'cities';
    protected $fillable = [
        'city_id','city_name','state_id', 'status',
        
    ];
}