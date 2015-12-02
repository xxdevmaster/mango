<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelsFilmsKeywords extends Model
{
    protected $table = "fk_channels_films_keywords";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'channels_id', 'films_id', 'description', 'keywords', 'locale'];
}
