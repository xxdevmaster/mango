<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilmSubtitles extends Model
{
    protected $table = "cc_subtitles";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'file', 'films_id', 'deleted'];
}
