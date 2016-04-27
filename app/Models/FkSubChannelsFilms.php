<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FkSubChannelsFilms extends Model
{
    protected $table = "fk_subchannels_films";

    public $timestamps = false;

    protected $fillable = ['id', 'subchannels_id', 'films_id', 'priority'];
}
