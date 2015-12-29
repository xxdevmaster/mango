<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmsMedia extends Model
{
    protected $table = "fk_films_media";

    protected $fillable = ['id', 'films_id', 'source', 'type', 'track_index', 'locale', 'def', 'deleted', 'uploaded'];

    public $timestamps = false;
}
