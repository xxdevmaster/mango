<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FilmsLanguages extends Model
{
   protected $table = "fk_films_languages";
	
   public $timestamps = false;

   protected $fillable = ['id', 'films_id', 'languages_id'];
}
