<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FilmsCountries extends Model
{
   protected $table = "fk_films_countries";
   
   protected $primaryKey = "films_id";
	
   public $timestamps = false;

   protected $fillable = ['id', 'countries_id', 'films_id'];
}
