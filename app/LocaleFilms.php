<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocaleFilms extends Model
{
   protected $table = "locale_films";
   
   public $timestamps = false;
   
   protected $fillable = ['id','films_id', 'title', 'synopsis', 'cover', 'locale', 'deleted', 'def'];
}
