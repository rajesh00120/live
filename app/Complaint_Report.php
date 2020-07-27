<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complaint_Report extends Model
{
    protected $table = 'complaint_report';
    protected $fillable = [
        'user_id', 'media_id', 'reports','created_at'
    ];
}
