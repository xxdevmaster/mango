<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelsFilms extends Model
{
    protected $table = "fk_channels_films";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'channels_id', 'films_id', 'priority'];
}
