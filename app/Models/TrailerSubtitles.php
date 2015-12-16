<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrailerSubtitles extends Model
{
    protected $table = "cc_tsubtitles";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'title', 'file', 'films_id', 'deleted'];
}
