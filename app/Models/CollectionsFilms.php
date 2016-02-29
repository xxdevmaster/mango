<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionsFilms extends Model
{
    protected $table = "fk_collections_films";
	
    public $timestamps = false;
	
	protected $fillable = ['id', 'collections_id', 'films_id', 'priority'];
}
