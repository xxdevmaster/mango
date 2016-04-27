<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FilmsGenres extends Model
{
   protected $table = "fk_films_genres";

   public $timestamps = false;

   protected $fillable = ['id', 'films_id', 'genres_id'];
}
