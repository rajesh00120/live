<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album_views extends Model
{
    protected $table = 'albums_views';
    protected $fillable = [
        'album_id', 'album_media_id', 'user_id'
    ];
}
