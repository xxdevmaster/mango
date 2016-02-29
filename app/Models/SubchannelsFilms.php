<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubchannelsFilms extends Model
{
    protected $table = "cc_subchannels_films";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'subchannels_id', 'films_id', 'priority'];
}
