<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $table = 'advertisement';
    protected $fillable = [
        'id','ad_images', 'ad_title','ad_url','ad_status', 'ad_country', 'ad_state','ad_city','created_at', 'updated_at',
        
    ];
}