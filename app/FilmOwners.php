<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilmOwners extends Model
{
    protected $table = "fk_films_owners";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'owner_id', 'films_id', 'type', 'role', 'deleted'];
}
