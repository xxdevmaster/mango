<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class FilmsGenres extends Model
{
   protected $table = "fk_films_genres";
   
   protected $primaryKey = "films_id";

   // TODO: ASAP The real primary key in database is field id , i have to change this point

   public $timestamps = false;

   protected $fillable = ['id', 'films_id', 'genres_id'];
}
